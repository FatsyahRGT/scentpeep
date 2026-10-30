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
        $this->get('/stok-reseller')->assertOk();
        $this->get('/katalog-stok')->assertRedirect(route('stocks.publicReseller'));
    }

    public function test_admin_can_render_all_views(): void
    {
        $routes = [
            '/dashboard',
            '/invoices',
            '/invoices/create',
            '/pre-orders',
            '/pre-orders/create',
            '/raw-materials',
            '/stocks/reseller',
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

    public function test_location_generates_distinct_contrasting_color_palettes(): void
    {
        $iwan = Location::create(['name' => 'IWAN', 'type' => Location::TYPE_WAREHOUSE]);
        $rizky = Location::create(['name' => 'RIZKY', 'type' => Location::TYPE_RESELLER]);
        $vanny = Location::create(['name' => 'VANNY', 'type' => Location::TYPE_RESELLER]);
        $newLoc1 = Location::create(['name' => 'Gudang Pusat Surabaya', 'type' => Location::TYPE_WAREHOUSE]);
        $newLoc2 = Location::create(['name' => 'Toko Distributor Medan', 'type' => Location::TYPE_RESELLER]);

        $iwanPalette = $iwan->getColorPalette();
        $rizkyPalette = $rizky->getColorPalette();
        $vannyPalette = $vanny->getColorPalette();
        $newLoc1Palette = $newLoc1->getColorPalette();
        $newLoc2Palette = $newLoc2->getColorPalette();

        $this->assertEquals('emerald', $iwanPalette['name']);
        $this->assertEquals('sapphire', $rizkyPalette['name']);
        $this->assertEquals('ruby', $vannyPalette['name']);

        // Check that static paletteForName matches instance getColorPalette
        $this->assertEquals($iwanPalette, Location::paletteForName('IWAN'));
        $this->assertEquals($rizkyPalette, Location::paletteForName('RIZKY'));
        $this->assertEquals($vannyPalette, Location::paletteForName('VANNY'));

        // Validate hex format for all color keys
        $hexPattern = '/^#[0-9a-f]{6}$/i';
        foreach ([$iwanPalette, $rizkyPalette, $vannyPalette, $newLoc1Palette, $newLoc2Palette] as $pal) {
            $this->assertMatchesRegularExpression($hexPattern, $pal['text']);
            $this->assertMatchesRegularExpression($hexPattern, $pal['bg']);
            $this->assertMatchesRegularExpression($hexPattern, $pal['border']);
            $this->assertMatchesRegularExpression($hexPattern, $pal['badge_bg']);
            $this->assertMatchesRegularExpression($hexPattern, $pal['badge_text']);
            $this->assertMatchesRegularExpression($hexPattern, $pal['pill_bg']);
        }

        // Distinct hues for different locations
        $this->assertNotEquals($newLoc1Palette['hue'], $newLoc2Palette['hue']);

        // Check that /stocks/reseller and /stok-reseller render successfully with all dynamic locations
        $this->actingAs($this->admin)->get('/stocks/reseller')
            ->assertOk()
            ->assertSee('IWAN')
            ->assertSee('RIZKY')
            ->assertSee('VANNY');

        $this->get('/stok-reseller')
            ->assertOk()
            ->assertSee('IWAN')
            ->assertSee('RIZKY')
            ->assertSee('VANNY');

        // Check that dashboard, invoices, transactions, and transfers render successfully with HSL badges
        $this->actingAs($this->admin)->get('/dashboard')->assertOk();
        $this->actingAs($this->admin)->get('/invoices')->assertOk();
        $this->actingAs($this->admin)->get('/transactions')->assertOk();
        $this->actingAs($this->admin)->get('/stocks/transfers')->assertOk();
    }
}
