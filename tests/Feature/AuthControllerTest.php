<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible_for_guests(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('login');
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('devices.list', absolute: false));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('devices.list', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create([
            'password' => 'secret-password',
        ]);

        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_customer_is_redirected_to_device_list_after_login(): void
    {
        $customer = User::factory()->customer()->create([
            'password' => 'secret-password',
        ]);

        Device::factory()->create(['user_id' => $customer->id]);

        $response = $this->post(route('login.post'), [
            'email' => $customer->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('devices.list', absolute: false));
    }

    public function test_guest_role_user_uses_simplified_layout(): void
    {
        $guest = User::factory()->guest()->create();

        $this->assertTrue($guest->usesSimplifiedLayout());
        $this->assertFalse($guest->usesAdminLayout());
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_inactive_authenticated_user_is_logged_out_on_dashboard(): void
    {
        $user = User::factory()->create(['status' => true]);

        $this->actingAs($user);

        $user->update(['status' => false]);

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
