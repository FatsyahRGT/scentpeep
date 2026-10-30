<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
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

    public function test_marking_invoice_paid_records_income_in_cash_transactions(): void
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

        $this->assertDatabaseHas('transactions', [
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'customer_name' => 'Dewi',
            'amount' => 180000,
            'invoice_no' => 'INV-TEST-PAID',
        ]);
    }

    public function test_pre_order_remains_pending_until_completed_then_adds_to_stock(): void
    {
        // 1. Create Pre-Order
        $response = $this->actingAs($this->admin)->post(route('pre-orders.store'), [
            'po_number' => 'PO-BATCH-01',
            'product_id' => $this->product->id,
            'location_id' => $this->iwan->id,
            'qty' => 15,
            'customer_name' => 'Pre Order Grosir',
        ]);

        $response->assertRedirect(route('pre-orders.index'));

        $po = PreOrder::where('po_number', 'PO-BATCH-01')->first();
        $this->assertNotNull($po);
        $this->assertEquals('PENDING', $po->status);

        // Stock at IWAN should NOT change yet (still 10)
        $this->assertEquals(10, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);

        // 2. Complete PO
        $completeResponse = $this->actingAs($this->admin)->post(route('pre-orders.complete', $po));
        $completeResponse->assertRedirect(route('pre-orders.index'));

        $this->assertEquals('SELESAI', $po->fresh()->status);
        // Stock at IWAN should now be 10 + 15 = 25
        $this->assertEquals(25, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);
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

        // Transaction for cash income should be created since it was changed to LUNAS
        $this->assertDatabaseHas('transactions', [
            'invoice_no' => 'INV-UPDATE-01-REV',
            'type' => Transaction::TYPE_INCOME,
            'amount' => 700000,
        ]);
    }

    public function test_admin_can_delete_invoice_and_restores_stock_and_removes_cash_record(): void
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

        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'customer_name' => 'Customer Hapus',
            'amount' => 540000,
            'invoice_no' => 'INV-DEL-01',
            'description' => 'Penjualan Invoice INV-DEL-01',
            'stock_effect' => Transaction::EFFECT_NONE,
            'created_by' => $this->admin->id,
        ]);

        // Stock was 10, simulate that 3 was deducted
        Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->decrement('qty', 3);
        $this->assertEquals(7, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);

        $response = $this->actingAs($this->admin)->delete(route('invoices.destroy', $invoice));

        $response->assertRedirect(route('invoices.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
        $this->assertDatabaseMissing('transactions', ['invoice_no' => 'INV-DEL-01']);

        // Stock should be restored to 10
        $this->assertEquals(10, Stock::where('product_id', $this->product->id)->where('location_id', $this->iwan->id)->first()->qty);
    }
}
