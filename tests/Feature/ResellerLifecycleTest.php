<?php

namespace Tests\Feature;

use App\Models\Location;
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

        $response->assertRedirect(route('dashboard'));
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
}
