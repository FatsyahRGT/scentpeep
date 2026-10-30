<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionAndStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Location $tokoGpi;
    protected Location $rizky;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'name' => 'Staff Test',
            'email' => 'staff@test.com',
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->tokoGpi = Location::create(['name' => 'TOKO GPI']);
        $this->rizky = Location::create(['name' => 'RIZKY']);

        $this->product = Product::create([
            'name' => 'Sauvage Test EDP',
            'code' => 'SP-TEST-01',
            'cost_price' => 100000,
            'sell_price' => 180000,
            'is_active' => true,
        ]);
    }

    public function test_can_record_income_without_stock_effect(): void
    {
        $response = $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Pendapatan Jasa',
            'description' => 'Konsultasi wewangian',
            'amount' => 500000,
            'stock_effect' => 'NONE',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'INCOME',
            'category' => 'Pendapatan Jasa',
            'amount' => 500000,
            'stock_effect' => 'NONE',
        ]);
    }

    public function test_pembelian_stok_increments_stock_directly(): void
    {
        $response = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'OUTCOME',
            'category' => 'Pembelian Stok',
            'amount' => 1000000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
            'stock_effect' => 'IN',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('stocks', [
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);
    }

    public function test_penjualan_decrements_stock_directly(): void
    {
        // Initial stock = 10
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        $response = $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 360000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 2,
            'stock_effect' => 'OUT',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('stocks', [
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 8,
        ]);
    }

    public function test_penjualan_fails_if_stock_insufficient(): void
    {
        // Initial stock = 2
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 900000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 5, // exceeds available stock 2
            'stock_effect' => 'OUT',
        ]);

        $response->assertSessionHasErrors(['qty']);

        // Stock remains unchanged
        $this->assertDatabaseHas('stocks', [
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
    }

    public function test_transaction_edit_reverses_old_stock_effect_and_applies_new(): void
    {
        // Initial stock = 10
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        // 1. First sale: 3 pcs OUT -> stock becomes 7
        $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 540000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 3,
            'stock_effect' => 'OUT',
        ]);

        $this->assertEquals(7, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);

        $tx = Transaction::latest('id')->first();

        // 2. Edit transaction to 1 pcs OUT -> should reverse 3 (+3 => 10) then deduct 1 (-1 => 9)
        $response = $this->actingAs($this->staff)->put(route('transactions.update', $tx), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 180000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 1,
            'stock_effect' => 'OUT',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertEquals(9, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
    }

    public function test_transaction_delete_reverses_stock_effect(): void
    {
        // Initial stock = 10
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        // Sale: 4 pcs OUT -> stock becomes 6
        $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 720000,
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 4,
            'stock_effect' => 'OUT',
        ]);

        $this->assertEquals(6, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);

        $tx = Transaction::latest('id')->first();

        // Delete transaction -> stock should be restored to 10
        $response = $this->actingAs($this->staff)->delete(route('transactions.destroy', $tx));
        $response->assertRedirect(route('transactions.index'));

        $this->assertEquals(10, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
    }

    public function test_transfer_stock_between_locations(): void
    {
        // Source stock = 15, Destination stock = 0
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 15,
        ]);

        $response = $this->actingAs($this->staff)->post(route('stocks.transfer.store'), [
            'from_location_id' => $this->tokoGpi->id,
            'to_location_id' => $this->rizky->id,
            'product_id' => $this->product->id,
            'qty' => 5,
            'notes' => 'Transfer uji coba',
        ]);

        $response->assertRedirect(route('stocks.transfers'));

        $this->assertEquals(10, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertEquals(5, Stock::where('location_id', $this->rizky->id)->where('product_id', $this->product->id)->first()->qty);

        $this->assertDatabaseHas('stock_transfers', [
            'from_location_id' => $this->tokoGpi->id,
            'to_location_id' => $this->rizky->id,
            'product_id' => $this->product->id,
            'qty' => 5,
        ]);
    }

    public function test_transfer_stock_edit_view_and_update_atomically(): void
    {
        // Initial: GPI = 20, RIZKY = 0
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 20,
        ]);

        // 1. Buat transfer 5 pcs
        $this->actingAs($this->admin)->post(route('stocks.transfer.store'), [
            'from_location_id' => $this->tokoGpi->id,
            'to_location_id' => $this->rizky->id,
            'product_id' => $this->product->id,
            'qty' => 5,
            'notes' => 'Transfer awal',
        ]);

        $transfer = StockTransfer::latest('id')->first();
        $this->assertNotNull($transfer);

        // Render edit view
        $editView = $this->actingAs($this->admin)->get(route('stocks.transfer.edit', $transfer));
        $editView->assertOk();
        $editView->assertSee('Edit Transfer Stok');

        // 2. Update transfer menjadi 8 pcs
        $updateResponse = $this->actingAs($this->admin)->put(route('stocks.transfer.update', $transfer), [
            'from_location_id' => $this->tokoGpi->id,
            'to_location_id' => $this->rizky->id,
            'product_id' => $this->product->id,
            'qty' => 8,
            'notes' => 'Transfer diedit jadi 8 pcs',
        ]);

        $updateResponse->assertRedirect(route('stocks.transfers'));

        // Stock GPI harus berkurang 8 (20 - 8 = 12), Stock RIZKY harus jadi 8
        $this->assertEquals(12, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertEquals(8, Stock::where('location_id', $this->rizky->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertEquals(8, $transfer->fresh()->qty);
    }

    public function test_transfer_stock_destroy_restores_stocks(): void
    {
        // Initial: GPI = 10, RIZKY = 0
        Stock::create([
            'location_id' => $this->tokoGpi->id,
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        // Transfer 4 pcs
        $this->actingAs($this->admin)->post(route('stocks.transfer.store'), [
            'from_location_id' => $this->tokoGpi->id,
            'to_location_id' => $this->rizky->id,
            'product_id' => $this->product->id,
            'qty' => 4,
            'notes' => 'Transfer untuk dihapus',
        ]);

        $transfer = StockTransfer::latest('id')->first();
        $this->assertEquals(6, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertEquals(4, Stock::where('location_id', $this->rizky->id)->where('product_id', $this->product->id)->first()->qty);

        // Batalkan transfer
        $destroyResponse = $this->actingAs($this->admin)->delete(route('stocks.transfer.destroy', $transfer));
        $destroyResponse->assertRedirect(route('stocks.transfers'));

        // Stok harus kembali utuh: GPI = 10, RIZKY = 0
        $this->assertEquals(10, Stock::where('location_id', $this->tokoGpi->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertEquals(0, Stock::where('location_id', $this->rizky->id)->where('product_id', $this->product->id)->first()->qty);
        $this->assertDatabaseMissing('stock_transfers', ['id' => $transfer->id]);
    }

    public function test_role_middleware_restricts_staff_from_master_data_and_users(): void
    {
        // Staff accessing users management -> should be redirected with 403 or error
        $response = $this->actingAs($this->staff)->get(route('users.index'));
        $response->assertRedirect(route('dashboard'));

        // Admin accessing users management -> allowed (200)
        $adminResponse = $this->actingAs($this->admin)->get(route('users.index'));
        $adminResponse->assertOk();
    }

    public function test_can_record_and_update_cash_transaction_with_selected_product_variant(): void
    {
        // 1. Simpan transaksi kas dengan varian parfum (stock_effect NONE)
        $response = $this->actingAs($this->staff)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 180000,
            'customer_name' => 'Budi Santoso',
            'channel' => 'Offline Store',
            'product_id' => $this->product->id,
            'qty' => 1,
            'stock_effect' => 'NONE',
        ]);

        $response->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'category' => 'Penjualan',
            'product_id' => $this->product->id,
            'qty' => 1,
            'customer_name' => 'Budi Santoso',
            'stock_effect' => 'NONE',
        ]);

        $transaction = Transaction::where('customer_name', 'Budi Santoso')->first();

        // 2. Update transaksi kas tersebut (misal ganti qty jadi 2 dan nominal)
        $updateResponse = $this->actingAs($this->admin)->put(route('transactions.update', $transaction), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Penjualan',
            'amount' => 360000,
            'customer_name' => 'Budi Santoso Updated',
            'channel' => 'WhatsApp',
            'product_id' => $this->product->id,
            'qty' => 2,
            'stock_effect' => 'NONE',
        ]);

        $updateResponse->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'customer_name' => 'Budi Santoso Updated',
            'channel' => 'WhatsApp',
            'qty' => 2,
            'amount' => 360000,
        ]);
    }

    public function test_reseller_stock_dashboard_filters_low_stock(): void
    {
        $prodReady = Product::create([
            'name' => 'Parfum Ready Banyak',
            'code' => 'READY-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodReady->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 10,
        ]);

        $prodLow = Product::create([
            'name' => 'Parfum Menipis Banget',
            'code' => 'LOW-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodLow->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 2,
        ]);

        $prodEmpty = Product::create([
            'name' => 'Parfum Habis Total',
            'code' => 'EMPTY-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodEmpty->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('stocks.reseller', ['filter_status' => 'low']));

        $response->assertOk();
        $response->assertSee('Parfum Menipis Banget');
        $response->assertSee('Menipis');

        $filteredData = $response->viewData('productStockData');
        $this->assertTrue($filteredData->contains(fn ($item) => $item['product']->id === $prodLow->id));
        $this->assertFalse($filteredData->contains(fn ($item) => $item['product']->id === $prodReady->id));
        $this->assertFalse($filteredData->contains(fn ($item) => $item['product']->id === $prodEmpty->id));
        $this->assertEquals(1, $response->viewData('metrics')['low_stock_count']);
    }

    public function test_public_reseller_dashboard_filters_low_stock(): void
    {
        $prodReady = Product::create([
            'name' => 'Publik Ready Banyak',
            'code' => 'PUB-READY-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodReady->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 8,
        ]);

        $prodLow = Product::create([
            'name' => 'Publik Menipis Banget',
            'code' => 'PUB-LOW-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodLow->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 1,
        ]);

        $prodEmpty = Product::create([
            'name' => 'Publik Habis Total',
            'code' => 'PUB-EMPTY-01',
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $prodEmpty->id,
            'location_id' => $this->tokoGpi->id,
            'qty' => 0,
        ]);

        $response = $this->get(route('stocks.publicReseller', ['filter_status' => 'low']));

        $response->assertOk();
        $response->assertSee('Katalog Ketersediaan & Sebaran Stok Parfum');
        $response->assertSee('Pantau parfum apa saja yang ready, jumlahnya, dan siapa yang memegang stoknya.');
        $response->assertSee('Publik Menipis Banget');

        $filteredData = $response->viewData('productStockData');
        $this->assertTrue($filteredData->contains(fn ($item) => $item['product']->id === $prodLow->id));
        $this->assertFalse($filteredData->contains(fn ($item) => $item['product']->id === $prodReady->id));
        $this->assertFalse($filteredData->contains(fn ($item) => $item['product']->id === $prodEmpty->id));
        $this->assertEquals(1, $response->viewData('metrics')['low_stock_count']);
    }

    public function test_can_record_pure_cash_ledger_transaction_without_affecting_stock(): void
    {
        // 1. Catat pengeluaran kas operasional (misal Iklan Shopee)
        $outcomeResponse = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'OUTCOME',
            'category' => 'Iklan Shopee',
            'amount' => 350000,
            'description' => 'Topup saldo Shopee Ads kampanye Payday',
            'customer_name' => 'Shopee Ads',
            'stock_effect' => 'NONE',
        ]);

        $outcomeResponse->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'OUTCOME',
            'category' => 'Iklan Shopee',
            'amount' => 350000,
            'stock_effect' => 'NONE',
            'customer_name' => 'Shopee Ads',
        ]);

        // 2. Catat pemasukan kas (misal Modal Masuk / Jasa)
        $incomeResponse = $this->actingAs($this->admin)->post(route('transactions.store'), [
            'tanggal' => now()->toDateString(),
            'type' => 'INCOME',
            'category' => 'Pendapatan Jasa',
            'amount' => 500000,
            'description' => 'Jasa peracikan formula parfum custom',
            'stock_effect' => 'NONE',
        ]);

        $incomeResponse->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'type' => 'INCOME',
            'category' => 'Pendapatan Jasa',
            'amount' => 500000,
            'stock_effect' => 'NONE',
        ]);
    }
}
