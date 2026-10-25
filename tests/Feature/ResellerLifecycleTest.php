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
}
