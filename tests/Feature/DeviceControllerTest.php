<?php

namespace Tests\Feature;

use App\Models\Devices;
use App\Models\User;
use App\Services\MqttService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Mockery;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(): User
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $this->be($user);
        return $user;
    }

    public function test_list_requires_authentication_and_renders_view(): void
    {
        $this->authenticate();

        $response = $this->get(route('devices.list'));

        $response->assertStatus(200);
        $response->assertViewIs('devices');
        $response->assertViewHas('devices');
    }

    public function test_create_form_loads_users_and_renders_view(): void
    {
        $this->authenticate();
        User::factory()->count(3)->create();

        $response = $this->get(route('devices.createform'));

        $response->assertStatus(200);
        $response->assertViewIs('devices-create');
        $response->assertViewHas('users');
    }

    public function test_create_validates_and_persists_device_then_redirects_with_success(): void
    {
        $this->authenticate();
        $owner = User::factory()->create();

        $payload = [
            'user_id' => $owner->id,
            'device_id' => 'dev-123',
            'status' => 'active',
        ];

        $response = $this->post(route('devices.create'), $payload);

        $response->assertRedirect(route('devices.list'));
        $response->assertSessionHas('success', 'Device added successfully');

        $this->assertDatabaseHas('assign_device', [
            'user_id' => $owner->id,
            'device_id' => 'dev-123',
            'is_active' => 1,
        ]);
    }

    public function test_create_shows_validation_errors_when_missing_fields(): void
    {
        $this->authenticate();
        $response = $this->from(route('devices.createform'))
            ->post(route('devices.create'), []);

        $response->assertRedirect(route('devices.createform'));
        $response->assertSessionHasErrors(['user_id', 'device_id', 'status']);
    }

    public function test_view_renders_device_view_with_device_data(): void
    {
        $this->authenticate();
        $owner = User::factory()->create();
        $device = Devices::create([
            'user_id' => $owner->id,
            'device_id' => 'dev-200',
            'is_active' => 1,
        ]);

        $response = $this->get(route('devices.view', ['id' => $device->id]));

        $response->assertStatus(200);
        $response->assertViewIs('devices-view');
        $response->assertViewHas('device', function ($v) use ($device) {
            return $v->id === $device->id;
        });
    }

    public function test_switch_publishes_to_mqtt_and_returns_success_json(): void
    {
        // Public route per routes/web.php
        $mock = Mockery::mock(MqttService::class);
        $mock->shouldReceive('publish')
            ->once()
            ->with('dev-500/request', Mockery::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return is_array($decoded)
                    && ($decoded['cmd'] ?? null) === 'setRelay';
            }))
            ->andReturn(true);
        $this->app->instance(MqttService::class, $mock);

        $response = $this->post(route('devices.switch'), [
            'deviceID' => 'dev-500',
            'relayID' => '0',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Message published',
            ]);
    }

    public function test_switch_validation_failure_returns_redirect_with_errors(): void
    {
        $response = $this->post(route('devices.switch'), []);

        // Controller redirects back on validation failure
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['deviceID', 'relayID']);
    }

    public function test_switch_handles_mqtt_exception_and_returns_error_json(): void
    {
        $mock = Mockery::mock(MqttService::class);
        $mock->shouldReceive('publish')
            ->once()
            ->andThrow(new \Exception('MQTT failure'));
        $this->app->instance(MqttService::class, $mock);

        $response = $this->post(route('devices.switch'), [
            'deviceID' => 'dev-err',
            'relayID' => '1',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => false,
                'error' => 'MQTT failure',
            ]);
    }
}
