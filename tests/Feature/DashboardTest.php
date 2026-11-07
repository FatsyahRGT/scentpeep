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
        $response->assertRedirect(route('stocks.reseller'));

        $resellerView = $this->actingAs($this->reseller)->get(route('stocks.reseller'));
        $resellerView->assertOk();
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

    public function test_dashboard_bestsellers_calculates_based_on_invoices(): void
    {
        \App\Models\Invoice::create([
            'invoice_no' => 'INV-BS-001',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Budi',
            'product_id' => $this->product->id,
            'qty' => 12,
            'location_id' => $this->gudang->id,
            'total_price' => 2400000,
            'payment_status' => 'LUNAS',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Varian Parfum Terlaris (Best Seller)');
        $response->assertSee('Sauvage Test');
        $response->assertSee('12 pcs');
        $response->assertSee('2.400.000');
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

    public function test_dashboard_sales_distribution_excludes_storage_location_names(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertOk();

        $salesDistribution = $response->viewData('salesDistribution');
        $salesNames = collect($salesDistribution)->pluck('name');

        // Gudang & Toko Reseller tidak boleh muncul sebagai nama sales person
        $this->assertFalse($salesNames->contains('Gudang Pusat'));
        $this->assertFalse($salesNames->contains('Toko Reseller Mitra'));
    }

    public function test_dashboard_seller_trend_defaults_to_12_months_and_supports_custom_year_and_month_filter(): void
    {
        // Buat data invoice tahun 2025 di bulan Mei
        \App\Models\Invoice::create([
            'invoice_no' => 'INV-2025-05',
            'sales_name' => 'RIZKY',
            'customer_name' => 'Budi',
            'product_id' => $this->product->id,
            'qty' => 20,
            'location_id' => $this->gudang->id,
            'total_price' => 3000000,
            'payment_status' => 'LUNAS',
            'tanggal' => '2025-05-15',
            'created_at' => '2025-05-15 10:00:00',
        ]);

        // 1. Tampilan 1 Tahun Penuh (12 bulan)
        $responseYear = $this->actingAs($this->admin)->get(route('dashboard', ['sales_year' => 2025]));
        $responseYear->assertOk();
        $trendDataYear = $responseYear->viewData('sellerTrendData');
        $this->assertCount(12, $trendDataYear['labels']);
        $this->assertEquals('Jan 2025', $trendDataYear['labels'][0]);
        $this->assertEquals('Des 2025', $trendDataYear['labels'][11]);
        $this->assertStringContainsString('Tahun 2025 - 1 Tahun Penuh', $trendDataYear['period_title']);

        $rizkyYear = collect($trendDataYear['datasets'])->firstWhere('name', 'RIZKY');
        $this->assertEquals(20, $rizkyYear['total_qty']);
        $this->assertEquals(20, $rizkyYear['qty'][4]); // Bulan ke-5 (indeks 4)

        // 2. Tampilan Kustom Bulan (Harian, Mei = 31 hari)
        $responseMonth = $this->actingAs($this->admin)->get(route('dashboard', ['sales_year' => 2025, 'sales_month' => 5]));
        $responseMonth->assertOk();
        $trendDataMonth = $responseMonth->viewData('sellerTrendData');
        $this->assertCount(31, $trendDataMonth['labels']);
        $this->assertEquals('01 Mei', $trendDataMonth['labels'][0]);
        $this->assertEquals('31 Mei', $trendDataMonth['labels'][30]);
        $this->assertStringContainsString('Bulan Mei 2025', $trendDataMonth['period_title']);

        $rizkyMonth = collect($trendDataMonth['datasets'])->firstWhere('name', 'RIZKY');
        $this->assertEquals(20, $rizkyMonth['total_qty']);
        $this->assertEquals(20, $rizkyMonth['qty'][14]); // Tanggal 15 (indeks 14)
    }

    public function test_dashboard_seller_trend_supports_ajax_json_response(): void
    {
        \App\Models\Invoice::create([
            'invoice_no' => 'INV-AJAX-01',
            'sales_name' => 'IWAN',
            'customer_name' => 'Doni',
            'product_id' => $this->product->id,
            'qty' => 5,
            'location_id' => $this->gudang->id,
            'total_price' => 750000,
            'payment_status' => 'LUNAS',
            'tanggal' => '2025-06-10',
            'created_at' => '2025-06-10 12:00:00',
        ]);

        $response = $this->actingAs($this->admin)->json('GET', route('dashboard'), [
            'sales_year' => 2025,
            'sales_month' => 6,
            'ajax_trend' => 1,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'labels',
            'datasets',
            'has_data',
            'total_volume',
            'total_omset',
            'available_years',
            'selected_year',
            'selected_month',
            'period_title',
            'period_subtitle',
            'period_top_seller',
        ]);
        $response->assertJsonPath('selected_year', 2025);
        $response->assertJsonPath('selected_month', 6);
        $this->assertEquals(30, count($response->json('labels')));
    }

    public function test_sales_dashboard_renders_with_seller_trend_and_filter(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.sales'));
        $response->assertOk();
        $response->assertSee('Tren Penjualan per Seller');
        $response->assertSee('sellerTrendYearSelect');
        $response->assertSee('sellerTrendMonthSelect');
        $response->assertViewHas('sellerTrendData');
    }
}
