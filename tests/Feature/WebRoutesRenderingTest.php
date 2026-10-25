<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRoutesRenderingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $reseller;

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

        Location::create([
            'name' => 'Toko Reseller A',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $this->reseller->id,
        ]);

        Product::create([
            'name' => 'Versace Eros',
            'code' => 'eros',
            'cost_price' => 150000,
            'sell_price' => 250000,
            'is_active' => true,
        ]);
    }

    public function test_guest_can_render_login_and_register(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_admin_can_render_all_views(): void
    {
        $routes = [
            '/dashboard',
            '/transactions',
            '/transactions/create?mode=stock',
            '/transactions/create?mode=cash',
            '/stocks',
            '/stocks/transfer',
            '/stocks/transfers',
            '/products',
            '/products/create',
            '/locations',
            '/locations/create',
            '/users',
            '/reports/transactions/excel',
            '/reports/transactions/pdf',
            '/reports/stocks/excel',
            '/reports/stocks/pdf',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $response->assertSuccessful();
        }
    }

    public function test_reseller_can_render_reseller_views(): void
    {
        $routes = [
            '/dashboard',
            '/transactions',
            '/transactions/create?mode=stock',
            '/stocks',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($this->reseller)->get($url);
            $response->assertOk();
        }
    }
}
