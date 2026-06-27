<?php

namespace Tests\Feature;

use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\MqttResponse;
use App\Models\User;
use App\Services\Mqtt\DeviceMqttService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class DeviceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): User
    {
        $admin = User::factory()->admin()->create([
            'password' => 'password',
        ]);
        $this->actingAs($admin);

        return $admin;
    }

    protected function authenticateUser(): User
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_list_requires_authentication_and_renders_view(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('devices.list'));

        $response->assertOk();
        $response->assertViewIs('devices');
        $response->assertViewHas('devices');
    }

    public function test_non_admin_only_sees_own_devices_in_list(): void
    {
        $user = $this->authenticateUser();
        $other = User::factory()->create();

        Device::factory()->create(['user_id' => $user->id, 'device_id' => 'mine-001']);
        Device::factory()->create(['user_id' => $other->id, 'device_id' => 'other-001']);

        $response = $this->get(route('devices.list'));

        $response->assertOk();
        $response->assertSee('mine-001');
        $response->assertDontSee('other-001');
    }

    public function test_guest_role_only_sees_own_devices_in_list(): void
    {
        $guest = User::factory()->guest()->create();
        $other = User::factory()->create();

        Device::factory()->create(['user_id' => $guest->id, 'device_id' => 'guest-001']);
        Device::factory()->create(['user_id' => $other->id, 'device_id' => 'other-002']);

        $this->actingAs($guest)
            ->get(route('devices.list'))
            ->assertOk()
            ->assertSee('guest-001')
            ->assertDontSee('other-002');
    }

    public function test_create_form_requires_admin(): void
    {
        $this->authenticateUser();

        $this->get(route('devices.createform'))->assertForbidden();
    }

    public function test_admin_can_view_create_form(): void
    {
        $this->authenticateAdmin();
        User::factory()->count(2)->create();

        $response = $this->get(route('devices.createform'));

        $response->assertOk();
        $response->assertViewIs('devices-create');
        $response->assertViewHas('users');
    }

    public function test_admin_can_create_active_device(): void
    {
        $this->authenticateAdmin();
        $owner = User::factory()->create();

        $response = $this->post(route('devices.create'), [
            'user_id' => $owner->id,
            'device_id' => 'dev-123',
            'device_type' => DeviceType::Panel->value,
            'status' => '1',
        ]);

        $response->assertRedirect(route('devices.list'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assign_device', [
            'user_id' => $owner->id,
            'device_id' => 'dev-123',
            'is_active' => 1,
            'device_type' => DeviceType::Panel->value,
        ]);
    }

    public function test_admin_can_create_inactive_device(): void
    {
        $this->authenticateAdmin();
        $owner = User::factory()->create();

        $this->post(route('devices.create'), [
            'user_id' => $owner->id,
            'device_id' => 'dev-inactive',
            'status' => '0',
        ]);

        $this->assertDatabaseHas('assign_device', [
            'device_id' => 'dev-inactive',
            'is_active' => 0,
        ]);
    }

    public function test_create_shows_validation_errors_when_missing_fields(): void
    {
        $this->authenticateAdmin();

        $response = $this->from(route('devices.createform'))
            ->post(route('devices.create'), []);

        $response->assertRedirect(route('devices.createform'));
        $response->assertSessionHasErrors(['user_id', 'device_id', 'status']);
    }

    public function test_view_renders_device_view_with_device_data(): void
    {
        $user = $this->authenticateUser();
        $device = Device::factory()->create([
            'user_id' => $user->id,
            'device_id' => 'dev-200',
        ]);

        $response = $this->get(route('devices.view', $device));

        $response->assertOk();
        $response->assertViewIs('devices-view');
        $response->assertViewHas('device', fn ($v) => $v->id === $device->id);
    }

    public function test_energy_meter_device_renders_energy_meter_view(): void
    {
        $user = $this->authenticateUser();
        $device = Device::factory()->energyMeter()->create([
            'user_id' => $user->id,
            'device_id' => '3C:E9:0E:CD:90:45',
        ]);

        $response = $this->get(route('devices.view', $device));

        $response->assertOk();
        $response->assertViewIs('energy-meter-view');
        $response->assertSee('3-Phase Energy Meter', false);
        $response->assertDontSee('Shutdown All', false);
    }

    public function test_user_cannot_view_someone_elses_device(): void
    {
        $this->authenticateUser();
        $device = Device::factory()->create();

        $this->get(route('devices.view', $device->id))->assertForbidden();
    }

    public function test_get_mqtt_data_returns_latest_response(): void
    {
        $user = $this->authenticateUser();
        $device = Device::factory()->create([
            'user_id' => $user->id,
            'device_id' => 'dev-mqtt',
        ]);

        MqttResponse::create([
            'device_id' => $device->device_id,
            'topic' => $device->device_id.'/response',
            'message' => ['cmd' => 'statusUpdate', 'data' => ['voltage' => 230]],
            'received_at' => now(),
        ]);

        $response = $this->postJson(route('devices.mqtt.data'), [
            'deviceID' => $device->device_id,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.data.voltage', 230);
    }

    public function test_switch_requires_authentication(): void
    {
        $this->post(route('devices.switch'), [
            'deviceID' => 'dev-500',
            'relayID' => '0',
        ])->assertRedirect(route('login'));
    }

    public function test_switch_publishes_to_mqtt_and_returns_success_json(): void
    {
        $user = $this->authenticateUser();
        Device::factory()->create([
            'user_id' => $user->id,
            'device_id' => 'dev-500',
        ]);

        $mock = Mockery::mock(DeviceMqttService::class);
        $mock->shouldReceive('toggleRelay')
            ->once()
            ->with('dev-500', '0', 0)
            ->andReturn(true);
        $this->app->instance(DeviceMqttService::class, $mock);

        $response = $this->post(route('devices.switch'), [
            'deviceID' => 'dev-500',
            'relayID' => '0',
            'status' => 0,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => true,
                'message' => 'Success',
            ]);
    }

    public function test_switch_validation_failure_returns_errors(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('devices.switch'), []);

        $response->assertSessionHasErrors(['deviceID', 'relayID']);
    }

    public function test_switch_handles_mqtt_exception_and_returns_error_json(): void
    {
        $user = $this->authenticateUser();
        Device::factory()->create([
            'user_id' => $user->id,
            'device_id' => 'dev-err',
        ]);

        $mock = Mockery::mock(DeviceMqttService::class);
        $mock->shouldReceive('toggleRelay')
            ->once()
            ->andThrow(new \Exception('MQTT failure'));
        $this->app->instance(DeviceMqttService::class, $mock);

        $response = $this->post(route('devices.switch'), [
            'deviceID' => 'dev-err',
            'relayID' => '1',
            'status' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => false,
                'error' => 'MQTT failure',
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
