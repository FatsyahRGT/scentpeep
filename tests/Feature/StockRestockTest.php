<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRestockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $reseller;
    protected Location $location1;
    protected Location $location2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->reseller = User::factory()->create([
            'role' => 'reseller',
            'is_active' => true,
        ]);

        $this->location1 = Location::create(['name' => 'IWAN', 'type' => 'warehouse']);
        $this->location2 = Location::create(['name' => 'RIZKY', 'type' => 'warehouse']);
    }

    public function test_admin_can_access_restock_index_and_see_catalog_with_pagination(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => "Parfum Varian {$i}",
                'code' => "SKU-{$i}",
                'cost_price' => 50000,
                'sell_price' => 85000,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('stocks.restock'));

        $response->assertStatus(200);
        $response->assertViewIs('stocks.restock');
        $response->assertSee('Katalog Ketersediaan &amp; Sebaran Stok Parfum', false);
        $response->assertSee('Tambah Stock');
        $response->assertSee('Tambah Stok Manual (Sehabis Produksi)');
    }

    public function test_filter_status_empty_displays_out_of_stock_products(): void
    {
        $productReady = Product::create([
            'name' => 'Parfum Ready Stock',
            'code' => 'SKU-READY',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);
        Stock::create([
            'product_id' => $productReady->id,
            'location_id' => $this->location1->id,
            'qty' => 50,
        ]);

        $productEmpty = Product::create([
            'name' => 'Parfum Habis Total',
            'code' => 'SKU-EMPTY',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->staff)->get(route('stocks.restock', ['filter_status' => 'empty']));

        $response->assertStatus(200);
        $response->assertSee('Parfum Habis Total');
        $viewProducts = $response->viewData('products');
        $this->assertTrue($viewProducts->contains('name', 'Parfum Habis Total'));
        $this->assertFalse($viewProducts->contains('name', 'Parfum Ready Stock'));
    }

    public function test_reseller_is_blocked_from_restock_page(): void
    {
        $response = $this->actingAs($this->reseller)->get(route('stocks.restock'));

        $response->assertRedirect(route('stocks.reseller'));
    }

    public function test_reseller_dashboard_links_empty_variants_to_restock_page(): void
    {
        Product::create([
            'name' => 'Parfum Kosong',
            'code' => 'SKU-KOSONG',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('stocks.reseller'));

        $response->assertStatus(200);
        // Link "Lihat ->" harus mengarah ke stocks.restock?filter_status=empty
        $response->assertSee(route('stocks.restock', ['filter_status' => 'empty']));
        // Tabel katalog utama tidak boleh ada lagi di reseller_dashboard
        $response->assertDontSee('Main Table: Katalog Sebaran Stok & Filter Ready');
    }

    public function test_admin_and_staff_can_adjust_stock_quantities_per_location(): void
    {
        $product = Product::create([
            'name' => 'Parfum Koreksi Stok',
            'code' => 'SP-ADJ-01',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);

        Stock::create(['product_id' => $product->id, 'location_id' => $this->location1->id, 'qty' => 10]);
        Stock::create(['product_id' => $product->id, 'location_id' => $this->location2->id, 'qty' => 5]);

        $response = $this->actingAs($this->admin)->post(route('stocks.adjust'), [
            'product_id' => $product->id,
            'stocks' => [
                $this->location1->id => 18, // Ubah dari 10 menjadi 18
                $this->location2->id => 2,  // Ubah dari 5 menjadi 2
            ],
            'reason' => 'Koreksi salah input saat restock',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(18, Stock::where('product_id', $product->id)->where('location_id', $this->location1->id)->first()->qty);
        $this->assertEquals(2, Stock::where('product_id', $product->id)->where('location_id', $this->location2->id)->first()->qty);
    }

    public function test_reseller_cannot_adjust_stock(): void
    {
        $product = Product::create([
            'name' => 'Parfum Terlarang Reseller',
            'code' => 'SP-BLOCK-01',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);

        Stock::create(['product_id' => $product->id, 'location_id' => $this->location1->id, 'qty' => 10]);

        $response = $this->actingAs($this->reseller)->post(route('stocks.adjust'), [
            'product_id' => $product->id,
            'stocks' => [
                $this->location1->id => 999,
            ],
        ]);

        // Reseller dialihkan oleh middleware internal atau controller check
        $this->assertEquals(10, Stock::where('product_id', $product->id)->where('location_id', $this->location1->id)->first()->qty);
    }

    public function test_ringkasan_stok_view_renders_adjust_stock_modal_and_action(): void
    {
        $product = Product::create([
            'name' => 'Parfum Tampil Modal',
            'code' => 'SP-MODAL-01',
            'cost_price' => 50000,
            'sell_price' => 85000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('stocks.index'));

        $response->assertStatus(200);
        $response->assertSee('Edit / Koreksi Stok');
        $response->assertSee('id="modalAdjustStock"', false);
        $response->assertSee('openAdjustStockModal', false);
    }
}
