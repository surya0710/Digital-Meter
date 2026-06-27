<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_redirected_from_dashboard_to_device_list(): void
    {
        $customer = User::factory()->customer()->create();
        Device::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('dashboard'));

        $response->assertRedirect(route('devices.list', absolute: false));
    }

    public function test_customer_only_sees_assigned_devices_in_list(): void
    {
        $customer = User::factory()->customer()->create();
        $other = User::factory()->create();

        Device::factory()->create(['user_id' => $customer->id, 'device_id' => 'cust-001']);
        Device::factory()->create(['user_id' => $other->id, 'device_id' => 'other-001']);

        $this->actingAs($customer)
            ->get(route('devices.list'))
            ->assertOk()
            ->assertSee('cust-001')
            ->assertDontSee('other-001');
    }

    public function test_customer_can_access_device_list(): void
    {
        $customer = User::factory()->customer()->create();
        Device::factory()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->get(route('devices.list'))
            ->assertOk()
            ->assertViewIs('devices');
    }

    public function test_admin_can_access_dashboard_and_device_list(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('devices.list'))
            ->assertOk();
    }

    public function test_non_admin_cannot_publish_mqtt_messages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('mqtt.publish'), [
                'topic' => 'test/topic',
                'message' => 'hello',
            ])
            ->assertForbidden();
    }

    public function test_admin_can_publish_mqtt_messages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('mqtt.publish'), [
                'topic' => 'test/topic',
                'message' => 'hello',
            ])
            ->assertOk();
    }

    public function test_non_admin_is_redirected_from_dashboard_to_device_list(): void
    {
        $guest = User::factory()->guest()->create();

        $this->actingAs($guest)
            ->get(route('dashboard'))
            ->assertRedirect(route('devices.list', absolute: false));
    }

    public function test_guest_can_access_device_list(): void
    {
        $guest = User::factory()->guest()->create();

        $this->actingAs($guest)
            ->get(route('devices.list'))
            ->assertOk()
            ->assertViewIs('devices');
    }

    public function test_guest_login_with_intended_dashboard_redirects_to_device_list(): void
    {
        $guest = User::factory()->guest()->create([
            'password' => 'secret-password',
        ]);
        Device::factory()->create(['user_id' => $guest->id]);

        $this->get(route('dashboard'));

        $response = $this->post(route('login.post'), [
            'email' => $guest->email,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('devices.list', absolute: false));
    }
}
