<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $reseller;
    protected Location $gudang;
    protected Location $tokoReseller;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->reseller = User::factory()->create([
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $this->gudang = Location::create([
            'name' => 'Gudang Pusat',
            'type' => Location::TYPE_WAREHOUSE,
        ]);

        $this->tokoReseller = Location::create([
            'name' => 'Toko Reseller Mitra',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $this->reseller->id,
        ]);

        $this->product = Product::create([
            'name' => 'Sauvage Test',
            'code' => 'dior',
            'cost_price' => 120000,
            'sell_price' => 200000,
            'is_active' => true,
        ]);
    }

    public function test_admin_dashboard_shows_critical_stocks_under_3_pcs(): void
    {
        // Stock of 2 pcs -> Critical (< 3)
        Stock::create([
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Peringatan Stok Menipis (< 3 pcs)');
        $response->assertSee('2 pcs');
        $response->assertSee('Sauvage Test');
    }

    public function test_kriteria_8_dashboard_saldo_equals_sum_income_minus_outcome(): void
    {
        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'amount' => 500000,
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_OUTCOME,
            'category' => 'Operasional',
            'amount' => 150000,
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertViewHas('netSaldo', 350000.0);
    }

    public function test_reseller_dashboard_scoped_to_their_data(): void
    {
        $response = $this->actingAs($this->reseller)->get(route('dashboard'));
        $response->assertOk();
        $response->assertViewHas('isReseller', true);
    }

    public function test_dashboard_bestsellers_calculates_based_on_product_sales_transactions(): void
    {
        // 1. Penjualan kas dengan product_id
        Transaction::create([
            'tanggal' => now()->toDateString(),
            'type' => Transaction::TYPE_INCOME,
            'category' => 'Penjualan',
            'product_id' => $this->product->id,
            'qty' => 5,
            'amount' => 1000000,
            'stock_effect' => Transaction::EFFECT_NONE,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Varian Parfum Terlaris (Best Seller)');
        $response->assertSee('Sauvage Test');
        $response->assertSee('5 pcs');
        $response->assertSee('1.000.000');
    }

    public function test_dashboard_renders_seller_sales_trend_chart_with_multi_seller_datasets(): void
    {
        \App\Models\Invoice::create([
            'invoice_no' => 'INV-TEST-001',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Budi',
            'product_id' => $this->product->id,
            'qty' => 15,
            'location_id' => $this->gudang->id,
            'total_price' => 2500000,
            'payment_status' => 'LUNAS',
            'created_at' => now()->startOfMonth()->subMonth(),
        ]);

        \App\Models\Invoice::create([
            'invoice_no' => 'INV-TEST-002',
            'sales_name' => 'IWAN',
            'customer_name' => 'Citra',
            'product_id' => $this->product->id,
            'qty' => 10,
            'location_id' => $this->gudang->id,
            'total_price' => 1800000,
            'payment_status' => 'LUNAS',
            'created_at' => now()->startOfMonth()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Tren Penjualan per Seller');
        $response->assertSee('RIZKY');
        $response->assertSee('IWAN');
        $response->assertViewHas('sellerTrendData');
        
        $trendData = $response->viewData('sellerTrendData');
        $this->assertArrayHasKey('labels', $trendData);
        $this->assertArrayHasKey('datasets', $trendData);
        $this->assertTrue($trendData['has_data']);

        $rizkyDs = collect($trendData['datasets'])->firstWhere('name', 'RIZKY');
        $this->assertNotNull($rizkyDs);
        $this->assertEquals(15, $rizkyDs['total_qty']);
        $this->assertEquals(\App\Models\Location::paletteForName('RIZKY')['pill_bg'], $rizkyDs['color']);
    }
}
