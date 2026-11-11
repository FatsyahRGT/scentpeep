<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAndProcurementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Location $iwan;
    protected Location $rizky;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->iwan = Location::create(['name' => 'IWAN']);
        $this->rizky = Location::create(['name' => 'RIZKY']);

        $this->product = Product::create([
            'name' => 'Scandalous EDP',
            'code' => 'SP-SCAN-01',
            'cost_price' => 100000,
            'sell_price' => 180000,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $this->product->id,
            'location_id' => $this->iwan->id,
            'qty' => 10,
        ]);

        Stock::create([
            'product_id' => $this->product->id,
            'location_id' => $this->rizky->id,
            'qty' => 5,
        ]);
    }

    public function test_can_create_invoice_with_custom_no_and_deducts_stock_automatically(): void
    {
        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-CUSTOM-001',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Rudi',
            'product_id' => $this->product->id,
            'qty' => 2,
            'location_id' => $this->iwan->id,
            'total_price' => 350000, // custom price with discount
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response->assertRedirect(route('invoices.index'));

        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-CUSTOM-001',
            'customer_name' => 'Rudi',
            'sales_name' => 'RIZKY',
            'qty' => 2,
            'payment_status' => 'BELUM LUNAS',
        ]);

        // Stock at IWAN was 10, now should be 8
        $this->assertEquals(8, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);
    }

    public function test_can_create_invoice_with_any_dynamic_user_as_sales(): void
    {
        $newSalesUser = User::factory()->create([
            'name' => 'Sarah Sales Team',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->assertTrue(Invoice::getSalesOptions()->contains('Sarah Sales Team'));

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-SARAH-01',
            'sales_name' => 'Sarah Sales Team',
            'customer_name' => 'Customer Sarah',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->iwan->id,
            'total_price' => 180000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-SARAH-01',
            'sales_name' => 'Sarah Sales Team',
        ]);
    }

    public function test_invoice_creation_fails_if_stock_insufficient(): void
    {
        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-EXCEED',
            'sales_name' => 'IWAN',
            'customer_name' => 'Budi',
            'product_id' => $this->product->id,
            'qty' => 20, // Only 5 available at Rizky
            'location_id' => $this->rizky->id,
            'total_price' => 3600000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response->assertSessionHasErrors('qty');
        $this->assertEquals(5, Stock::where('product_id', $this->product->id)->where('location_id', $this->rizky->id)->first()->qty);
    }

    public function test_marking_invoice_paid_updates_status_without_inserting_cash_transaction(): void
    {
        $invoice = Invoice::create([
            'invoice_no' => 'INV-TEST-PAID',
            'sales_name' => 'VANNY',
            'customer_name' => 'Dewi',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->iwan->id,
            'total_price' => 180000,
            'payment_status' => 'BELUM LUNAS',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('invoices.markPaid', $invoice));

        $response->assertRedirect();
        $this->assertEquals('LUNAS', $invoice->fresh()->payment_status);

        // Kas dipisahkan secara manual, tidak otomatis masuk ke transactions
        $this->assertDatabaseMissing('transactions', [
            'invoice_no' => 'INV-TEST-PAID',
        ]);
    }

    public function test_pre_order_locks_allocation_and_only_deducts_physical_stock_when_completed(): void
    {
        $iwanStock = Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first();
        $this->assertEquals(10, $iwanStock->qty);

        // 1. Buat Invoice Pre-Order (PO) sebanyak 4 pcs
        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-PO-BUDI',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'sales_name' => 'RIZKY',
            'customer_name' => 'Pak Budi PO',
            'product_id' => $this->product->id,
            'qty' => 4,
            'location_id' => $this->iwan->id,
            'total_price' => 720000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response->assertRedirect(route('invoices.index', ['tab' => 'po_pending']));

        $poInvoice = Invoice::where('invoice_no', 'INV-PO-BUDI')->first();
        $this->assertNotNull($poInvoice);
        $this->assertTrue($poInvoice->isPreOrder());
        $this->assertTrue($poInvoice->isPoPending());

        // Tahap 1: Stok fisik di IWAN TIDAK terpotong (masih 10 pcs), tapi jatah PO terkunci 4 pcs, siap jual 6 pcs
        $iwanStock->refresh();
        $this->assertEquals(10, $iwanStock->qty);
        $this->assertEquals(4, $iwanStock->getReservedPoQty());
        $this->assertEquals(6, $iwanStock->getAvailableQty());

        // 2. Coba jual langsung melebihi sisa siap jual (misal minta 7 pcs, padahal siap jual hanya 6) -> Harus DITOLAK
        $failResponse = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-OVER-SALE',
            'order_type' => Invoice::ORDER_TYPE_REGULAR,
            'sales_name' => 'RIZKY',
            'customer_name' => 'Pelanggan Umum',
            'product_id' => $this->product->id,
            'qty' => 7,
            'location_id' => $this->iwan->id,
            'total_price' => 1260000,
            'payment_status' => 'LUNAS',
        ]);
        $failResponse->assertSessionHasErrors('qty');

        // 3. Jual langsung sesuai jatah siap jual (6 pcs) -> BERHASIL memotong fisik
        $successResponse = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-REGULAR-OK',
            'order_type' => Invoice::ORDER_TYPE_REGULAR,
            'sales_name' => 'RIZKY',
            'customer_name' => 'Pelanggan Umum',
            'product_id' => $this->product->id,
            'qty' => 6,
            'location_id' => $this->iwan->id,
            'total_price' => 1080000,
            'payment_status' => 'LUNAS',
        ]);
        $successResponse->assertRedirect(route('invoices.index'));

        // Fisik sekarang 10 - 6 = 4 pcs (pas untuk jatah PO Pak Budi)
        $iwanStock->refresh();
        $this->assertEquals(4, $iwanStock->qty);
        $this->assertEquals(4, $iwanStock->getReservedPoQty());
        $this->assertEquals(0, $iwanStock->getAvailableQty());

        // 4. Tahap 3: Barang diserahkan ke Pak Budi (Klik "Selesaikan PO")
        $completeResponse = $this->actingAs($this->admin)->post(route('invoices.completePo', $poInvoice));
        $completeResponse->assertRedirect();

        $poInvoice->refresh();
        $this->assertTrue($poInvoice->isPoCompleted());
        $this->assertNotNull($poInvoice->po_completed_at);

        // Stok fisik resmi terpotong 4 pcs (sekarang 0 pcs), kuota PO kembali 0
        $iwanStock->refresh();
        $this->assertEquals(0, $iwanStock->qty);
        $this->assertEquals(0, $iwanStock->getReservedPoQty());
        $this->assertEquals(0, $iwanStock->getAvailableQty());
    }

    public function test_cannot_complete_po_if_physical_stock_is_insufficient(): void
    {
        $stock = Stock::where('product_id', $this->product->id)->where('location_id', $this->rizky->id)->first();
        $this->assertEquals(5, $stock->qty);

        // Buat PO 8 pcs (stok fisik hanya 5)
        $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-PO-SHORTAGE',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'sales_name' => 'RIZKY',
            'customer_name' => 'Pelanggan PO Kurang',
            'product_id' => $this->product->id,
            'qty' => 8,
            'location_id' => $this->rizky->id,
            'total_price' => 1440000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $po = Invoice::where('invoice_no', 'INV-PO-SHORTAGE')->first();
        $this->assertNotNull($po);

        // Coba selesaikan saat fisik belum restock (fisik 5 < diminta 8) -> Harus gagal validasi
        $response = $this->actingAs($this->admin)->post(route('invoices.completePo', $po));
        $response->assertSessionHasErrors('stock');
        $this->assertTrue($po->fresh()->isPoPending());
        $this->assertEquals(5, $stock->fresh()->qty);
    }

    public function test_destroy_pending_po_does_not_increment_physical_stock_while_destroy_completed_po_does(): void
    {
        $stock = Stock::where('product_id', $this->product->id)->where('location_id', $this->rizky->id)->first();
        $this->assertEquals(5, $stock->qty);

        // 1. Pending PO dihapus -> Stok fisik tetap 5
        $pendingPo = Invoice::create([
            'invoice_no' => 'INV-PO-PENDING-DEL',
            'tanggal' => now()->toDateString(),
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer Batal',
            'product_id' => $this->product->id,
            'qty' => 3,
            'location_id' => $this->rizky->id,
            'total_price' => 540000,
            'payment_status' => 'BELUM LUNAS',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'po_status' => Invoice::PO_STATUS_PENDING,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->delete(route('invoices.destroy', $pendingPo));
        $this->assertEquals(5, $stock->fresh()->qty);

        // 2. Completed PO dihapus -> Stok fisik dikembalikan +3 (menjadi 8)
        $completedPo = Invoice::create([
            'invoice_no' => 'INV-PO-DONE-DEL',
            'tanggal' => now()->toDateString(),
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer Retur Selesai',
            'product_id' => $this->product->id,
            'qty' => 3,
            'location_id' => $this->rizky->id,
            'total_price' => 540000,
            'payment_status' => 'LUNAS',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'po_status' => Invoice::PO_STATUS_COMPLETED,
            'po_completed_at' => now(),
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->delete(route('invoices.destroy', $completedPo));
        $this->assertEquals(8, $stock->fresh()->qty);
    }

    public function test_can_create_po_even_when_physical_stock_is_zero(): void
    {
        $zeroProduct = Product::create([
            'name' => 'Parfum Langka 0 Pcs',
            'code' => 'SP-ZERO-01',
            'cost_price' => 150000,
            'sell_price' => 250000,
            'is_active' => true,
        ]);

        Stock::create([
            'product_id' => $zeroProduct->id,
            'location_id' => $this->iwan->id,
            'qty' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-PO-ZERO-STK',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer PO Inden',
            'product_id' => $zeroProduct->id,
            'qty' => 10,
            'location_id' => $this->iwan->id,
            'total_price' => 2500000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response->assertRedirect(route('invoices.index', ['tab' => 'po_pending']));
        $this->assertDatabaseHas('invoices', [
            'invoice_no' => 'INV-PO-ZERO-STK',
            'order_type' => Invoice::ORDER_TYPE_PREORDER,
            'po_status' => Invoice::PO_STATUS_PENDING,
            'qty' => 10,
        ]);

        $stock = Stock::where('product_id', $zeroProduct->id)->where('location_id', $this->iwan->id)->first();
        $this->assertEquals(0, $stock->qty);
        $this->assertEquals(10, $stock->getReservedPoQty());
        $this->assertEquals(0, $stock->getAvailableQty());
    }

    public function test_raw_material_procurement_and_consumption(): void
    {
        $material = RawMaterial::create([
            'name' => 'Box Packaging 30ml',
            'category' => 'BOX',
            'stock_qty' => 50,
            'min_stock' => 10,
        ]);

        // 1. Procure 100 pcs with Rp 200.000 expense
        $response = $this->actingAs($this->admin)->post(route('raw-materials.procure', $material), [
            'qty' => 100,
            'amount' => 200000,
            'supplier' => 'Pabrik Dus Karton',
            'note' => 'Beli kardus baru',
        ]);

        $response->assertRedirect(route('raw-materials.index'));
        $this->assertEquals(150, $material->fresh()->stock_qty);

        // Should log in transactions (Buku Kas)
        $this->assertDatabaseHas('transactions', [
            'type' => Transaction::TYPE_OUTCOME,
            'category' => 'Bahan Baku',
            'amount' => 200000,
        ]);

        // 2. Consume 30 pcs
        $consumeResponse = $this->actingAs($this->admin)->post(route('raw-materials.consume', $material), [
            'qty' => 30,
            'note' => 'Packing produksi',
        ]);

        $consumeResponse->assertRedirect(route('raw-materials.index'));
        $this->assertEquals(120, $material->fresh()->stock_qty);
    }

    public function test_manual_restock_after_perfume_production(): void
    {
        $response = $this->actingAs($this->admin)->post(route('stocks.manualRestock'), [
            'product_id' => $this->product->id,
            'location_id' => $this->rizky->id,
            'qty' => 8,
            'notes' => 'Batch restock botol siap jual',
        ]);

        $response->assertRedirect();
        // Was 5, now should be 13
        $this->assertEquals(13, Stock::where('product_id', $this->product->id)->where('location_id', $this->rizky->id)->first()->qty);
    }

    public function test_admin_can_view_invoice_edit_page(): void
    {
        $invoice = Invoice::create([
            'invoice_no' => 'INV-EDIT-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer Edit Test',
            'product_id' => $this->product->id,
            'qty' => 2,
            'location_id' => $this->iwan->id,
            'total_price' => 360000,
            'payment_status' => 'BELUM LUNAS',
        ]);

        $response = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $response->assertOk();
        $response->assertSee('Edit Invoice INV-EDIT-01');
        $response->assertSee('Customer Edit Test');
        $response->assertSee('value="360000"', false);
        $response->assertDontSee('value="360000.00"', false);
    }

    public function test_invoices_index_renders_customer_filter_and_filters_by_customer_name(): void
    {
        \App\Models\Customer::create(['name' => 'IDA PG']);
        \App\Models\Customer::create(['name' => 'Budi Santoso']);

        Invoice::create([
            'invoice_no' => 'INV-CUST-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'IDA PG',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->iwan->id,
            'total_price' => 180000,
            'payment_status' => 'LUNAS',
        ]);

        Invoice::create([
            'invoice_no' => 'INV-CUST-02',
            'sales_name' => 'IWAN',
            'customer_name' => 'Budi Santoso',
            'product_id' => $this->product->id,
            'qty' => 2,
            'location_id' => $this->iwan->id,
            'total_price' => 360000,
            'payment_status' => 'LUNAS',
        ]);

        // 1. Check dropdown presence
        $indexRes = $this->actingAs($this->admin)->get(route('invoices.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('name="customer_name"', false);
        $indexRes->assertSee('Semua Customer');
        $indexRes->assertSee('IDA PG');
        $indexRes->assertSee('Budi Santoso');

        // 2. Filter by customer_name = 'IDA PG'
        $filterRes = $this->actingAs($this->admin)->get(route('invoices.index', ['customer_name' => 'IDA PG']));
        $filterRes->assertOk();
        $filterRes->assertSee('INV-CUST-01');
        $filterRes->assertDontSee('INV-CUST-02');
        $filterRes->assertSee('Reset Semua Filter');
    }

    public function test_admin_can_update_invoice_and_adjusts_stock_atomically(): void
    {
        // Initial stock at IWAN is 10
        $invoice = Invoice::create([
            'invoice_no' => 'INV-UPDATE-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Budi Lama',
            'product_id' => $this->product->id,
            'qty' => 2,
            'location_id' => $this->iwan->id,
            'total_price' => 360000,
            'payment_status' => 'BELUM LUNAS',
        ]);
        // Stock decreased to 8
        Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->decrement('qty', 2);

        // Update to 4 pcs and change customer name & location to RIZKY (which has 5 pcs)
        $response = $this->actingAs($this->admin)->put(route('invoices.update', $invoice), [
            'invoice_no' => 'INV-UPDATE-01-REV',
            'sales_name' => 'IWAN',
            'customer_name' => 'Budi Baru',
            'product_id' => $this->product->id,
            'qty' => 4,
            'location_id' => $this->rizky->id,
            'total_price' => 700000,
            'payment_status' => 'LUNAS',
            'notes' => 'Catatan revisi',
        ]);

        $response->assertRedirect(route('invoices.index'));

        // Old location IWAN should be restored to 10
        $this->assertEquals(10, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);

        // New location RIZKY was 5, now should be 1 (5 - 4)
        $this->assertEquals(1, Stock::where('product_id', $this->product->id)->where('location_id', $this->rizky->id)->first()->qty);

        // Invoice record updated
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_no' => 'INV-UPDATE-01-REV',
            'customer_name' => 'Budi Baru',
            'sales_name' => 'IWAN',
            'qty' => 4,
            'payment_status' => 'LUNAS',
        ]);

        // Kas dipisahkan secara manual, tidak otomatis masuk ke transactions
        $this->assertDatabaseMissing('transactions', [
            'invoice_no' => 'INV-UPDATE-01-REV',
        ]);
    }

    public function test_admin_can_delete_invoice_and_restores_stock(): void
    {
        $invoice = Invoice::create([
            'invoice_no' => 'INV-DEL-01',
            'sales_name' => 'IWAN',
            'customer_name' => 'Customer Hapus',
            'product_id' => $this->product->id,
            'qty' => 3,
            'location_id' => $this->iwan->id,
            'total_price' => 540000,
            'payment_status' => 'LUNAS',
            'created_by' => $this->admin->id,
        ]);

        // Stock was 10, simulate that 3 was deducted
        Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->decrement('qty', 3);
        $this->assertEquals(7, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);

        $response = $this->actingAs($this->admin)->delete(route('invoices.destroy', $invoice));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

        // Stock should be restored to 10
        $this->assertEquals(10, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);
    }

    public function test_can_create_and_update_invoice_with_custom_tanggal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'invoice_no' => 'INV-CUSTOM-DATE',
            'tanggal' => '2026-10-15',
            'sales_name' => 'IWAN',
            'customer_name' => 'Pak Joko',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->iwan->id,
            'total_price' => 180000,
            'payment_status' => 'LUNAS',
        ]);

        $response->assertRedirect(route('invoices.index'));

        $invoice = Invoice::where('invoice_no', 'INV-CUSTOM-DATE')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('2026-10-15', $invoice->tanggal->format('Y-m-d'));
        // Duit tidak otomatis masuk ke kas
        $this->assertDatabaseMissing('transactions', ['invoice_no' => 'INV-CUSTOM-DATE']);

        // Update tanggal ke tanggal lain
        $updateResponse = $this->actingAs($this->admin)->put(route('invoices.update', $invoice), [
            'invoice_no' => 'INV-CUSTOM-DATE',
            'tanggal' => '2026-10-25',
            'sales_name' => 'IWAN',
            'customer_name' => 'Pak Joko',
            'product_id' => $this->product->id,
            'qty' => 1,
            'location_id' => $this->iwan->id,
            'total_price' => 180000,
            'payment_status' => 'LUNAS',
        ]);

        $updateResponse->assertRedirect(route('invoices.index'));
        $this->assertEquals('2026-10-25', $invoice->fresh()->tanggal->format('Y-m-d'));
    }

    public function test_invoices_index_can_filter_by_year_and_month_and_displays_yoy_metrics(): void
    {
        $currentYear = now()->year;
        $prevYear = $currentYear - 1;

        // Invoice current year (Bulan 10 / Oktober)
        $invCY = Invoice::create([
            'invoice_no' => 'INV-CY-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer Ini',
            'product_id' => $this->product->id,
            'qty' => 5,
            'location_id' => $this->iwan->id,
            'total_price' => 900000,
            'payment_status' => 'LUNAS',
            'created_by' => $this->admin->id,
        ]);
        $invCY->created_at = Carbon::create($currentYear, 10, 15, 12, 0, 0);
        $invCY->save();

        // Invoice previous year
        $invPY = Invoice::create([
            'invoice_no' => 'INV-PY-01',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Customer Lalu',
            'product_id' => $this->product->id,
            'qty' => 3,
            'location_id' => $this->iwan->id,
            'total_price' => 540000,
            'payment_status' => 'LUNAS',
            'created_by' => $this->admin->id,
        ]);
        $invPY->created_at = Carbon::create($prevYear, 10, 10, 10, 0, 0);
        $invPY->save();

        // Test normal access
        $response = $this->actingAs($this->admin)->get(route('invoices.index'));
        $response->assertOk();
        $response->assertSee('Evaluasi YoY');
        $response->assertSee('INV-CY-01');
        $response->assertSee('INV-PY-01');

        // Test dedicated evaluation page
        $resEval = $this->actingAs($this->admin)->get(route('invoices.evaluation', ['year' => $currentYear, 'compare_year' => $prevYear]));
        $resEval->assertOk();
        $resEval->assertSee('Ringkasan Evaluasi');
        $resEval->assertSee('Komparasi Performa Bulanan');

        // Test filter by current year only
        $resFilterYear = $this->actingAs($this->admin)->get(route('invoices.index', ['year' => $currentYear]));
        $resFilterYear->assertOk();
        $resFilterYear->assertSee('INV-CY-01');
        $resFilterYear->assertDontSee('INV-PY-01');

        // Test filter by previous year
        $resFilterPrev = $this->actingAs($this->admin)->get(route('invoices.index', ['year' => $prevYear]));
        $resFilterPrev->assertOk();
        $resFilterPrev->assertSee('INV-PY-01');
        $resFilterPrev->assertDontSee('INV-CY-01');

        // Test filter by month (bulan 10)
        $resFilterMonth = $this->actingAs($this->admin)->get(route('invoices.index', ['year' => $currentYear, 'month' => 10]));
        $resFilterMonth->assertOk();
        $resFilterMonth->assertSee('INV-CY-01');

        // Test filter by another month (bulan 1)
        $resFilterEmpty = $this->actingAs($this->admin)->get(route('invoices.index', ['year' => $currentYear, 'month' => 1]));
        $resFilterEmpty->assertOk();
        $resFilterEmpty->assertDontSee('INV-CY-01');
    }
}
