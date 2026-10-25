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
}
