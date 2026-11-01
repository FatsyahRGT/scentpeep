<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResellerLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_reseller_can_register_and_starts_as_pending(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'Reseller Bandung',
            'email' => 'reseller@bandung.com',
            'location_name' => 'Toko Parfum Bandung',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'reseller@bandung.com',
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_PENDING,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'Toko Parfum Bandung',
            'type' => Location::TYPE_RESELLER,
        ]);
    }

    public function test_pending_reseller_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'pending@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'pending@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_can_approve_pending_reseller(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $reseller = User::factory()->create([
            'email' => 'reseller2@test.com',
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('users.approve', $reseller));
        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $reseller->id,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);
    }

    public function test_approved_reseller_can_login(): void
    {
        $reseller = User::factory()->create([
            'email' => 'approved@test.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_RESELLER,
            'status' => User::STATUS_ACTIVE,
            'is_active' => true,
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'approved@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('stocks.reseller'));
        $this->assertAuthenticatedAs($reseller);
    }

    public function test_admin_can_create_reseller_user_internally_with_location(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Reseller Solo',
            'email' => 'solo@reseller.com',
            'password' => 'solo12345',
            'role' => User::ROLE_RESELLER,
            'location_name' => 'Gudang Solo',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'solo@reseller.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::ROLE_RESELLER, $user->role);
        $this->assertTrue($user->is_active);

        $this->assertDatabaseHas('locations', [
            'name' => 'Gudang Solo',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_update_reseller_user_and_location(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $reseller = User::factory()->create([
            'name' => 'Reseller Jogja',
            'email' => 'jogja@reseller.com',
            'role' => User::ROLE_RESELLER,
            'is_active' => true,
        ]);

        $location = Location::create([
            'name' => 'Jogja Central',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $reseller->id,
        ]);

        $response = $this->actingAs($admin)->put(route('users.update', $reseller), [
            'name' => 'Reseller Jogja Istimewa',
            'email' => 'jogja_new@reseller.com',
            'role' => User::ROLE_RESELLER,
            'location_name' => 'Jogja Istimewa Store',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $reseller->id,
            'name' => 'Reseller Jogja Istimewa',
            'email' => 'jogja_new@reseller.com',
        ]);

        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Jogja Istimewa Store',
        ]);
    }

    public function test_reseller_is_blocked_from_internal_routes(): void
    {
        $reseller = User::factory()->create([
            'role' => User::ROLE_RESELLER,
            'is_active' => true,
        ]);

        // Buku Kas
        $this->actingAs($reseller)->get(route('transactions.index'))
            ->assertRedirect(route('stocks.reseller'))
            ->assertSessionHas('error');

        // Pengadaan Barang Mentah
        $this->actingAs($reseller)->get(route('raw-materials.index'))
            ->assertRedirect(route('stocks.reseller'))
            ->assertSessionHas('error');

        // Transfer Stok
        $this->actingAs($reseller)->get(route('stocks.transfers'))
            ->assertRedirect(route('stocks.reseller'))
            ->assertSessionHas('error');

        // Export Laporan
        $this->actingAs($reseller)->get(route('reports.transactions.excel'))
            ->assertRedirect(route('stocks.reseller'))
            ->assertSessionHas('error');
    }

    public function test_reseller_visiting_dashboard_is_redirected_to_reseller_stocks(): void
    {
        $reseller = User::factory()->create([
            'role' => User::ROLE_RESELLER,
            'is_active' => true,
        ]);

        $this->actingAs($reseller)->get(route('dashboard'))
            ->assertRedirect(route('stocks.reseller'));
    }

    public function test_reseller_can_only_see_and_manage_own_invoices(): void
    {
        $reseller1 = User::factory()->create([
            'name' => 'Reseller Satu',
            'email' => 'satu@test.com',
            'role' => User::ROLE_RESELLER,
            'is_active' => true,
        ]);
        $loc1 = Location::create([
            'name' => 'Toko Satu',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $reseller1->id,
        ]);

        $reseller2 = User::factory()->create([
            'name' => 'Reseller Dua',
            'email' => 'dua@test.com',
            'role' => User::ROLE_RESELLER,
            'is_active' => true,
        ]);
        $loc2 = Location::create([
            'name' => 'Toko Dua',
            'type' => Location::TYPE_RESELLER,
            'user_id' => $reseller2->id,
        ]);

        $product = Product::create([
            'code' => 'TEST01',
            'name' => 'Parfum Reseller Test',
            'cost_price' => 100000,
            'sell_price' => 150000,
            'is_active' => true,
        ]);
        Stock::create(['product_id' => $product->id, 'location_id' => $loc1->id, 'qty' => 10]);
        Stock::create(['product_id' => $product->id, 'location_id' => $loc2->id, 'qty' => 10]);

        $inv1 = Invoice::create([
            'invoice_no' => 'INV-RES-001',
            'sales_name' => $reseller1->name,
            'customer_name' => 'Customer A',
            'product_id' => $product->id,
            'qty' => 2,
            'location_id' => $loc1->id,
            'total_price' => 300000,
            'payment_status' => Invoice::STATUS_UNPAID,
            'created_by' => $reseller1->id,
        ]);

        $inv2 = Invoice::create([
            'invoice_no' => 'INV-RES-002',
            'sales_name' => $reseller2->name,
            'customer_name' => 'Customer B',
            'product_id' => $product->id,
            'qty' => 1,
            'location_id' => $loc2->id,
            'total_price' => 150000,
            'payment_status' => Invoice::STATUS_UNPAID,
            'created_by' => $reseller2->id,
        ]);

        // Reseller 1 visits invoices index
        $response = $this->actingAs($reseller1)->get(route('invoices.index'));
        $response->assertOk();
        $response->assertSee('INV-RES-001');
        $response->assertDontSee('INV-RES-002');

        // Reseller 1 cannot edit or delete Reseller 2's invoice
        $this->actingAs($reseller1)->get(route('invoices.edit', $inv2))
            ->assertForbidden();

        $this->actingAs($reseller1)->delete(route('invoices.destroy', $inv2))
            ->assertForbidden();

        $this->actingAs($reseller1)->post(route('invoices.markPaid', $inv2))
            ->assertForbidden();

        // Reseller 1 CAN edit their own invoice
        $this->actingAs($reseller1)->get(route('invoices.edit', $inv1))
            ->assertOk();
    }
}
