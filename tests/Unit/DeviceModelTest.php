<?php

namespace Tests\Unit;

use App\Enums\DeviceType;
use App\Enums\UserRole;
use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_device_uses_panel_view(): void
    {
        $device = Device::factory()->create(['device_type' => DeviceType::Panel]);

        $this->assertTrue($device->isPanel());
        $this->assertFalse($device->isEnergyMeter());
        $this->assertSame('devices-view', $device->dashboardView());
    }

    public function test_energy_meter_device_uses_meter_view(): void
    {
        $device = Device::factory()->energyMeter()->create();

        $this->assertTrue($device->isEnergyMeter());
        $this->assertSame('energy-meter-view', $device->dashboardView());
    }

    public function test_legacy_energy_meter_id_is_detected(): void
    {
        $legacyId = config('digital-meter.legacy_energy_meter_ids.0');

        $device = Device::factory()->create([
            'device_id' => $legacyId,
            'device_type' => DeviceType::Panel,
        ]);

        $this->assertTrue($device->isEnergyMeter());
        $this->assertSame('energy-meter-view', $device->dashboardView());
    }

    public function test_mqtt_topics_are_derived_from_device_id(): void
    {
        $device = Device::factory()->create(['device_id' => 'AA:BB:CC:DD:EE:FF']);

        $this->assertSame('AA:BB:CC:DD:EE:FF/request', $device->mqttRequestTopic());
        $this->assertSame('AA:BB:CC:DD:EE:FF/response', $device->mqttResponseTopic());
    }

    public function test_user_has_devices_relationship(): void
    {
        $user = User::factory()->create();
        Device::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->devices);
    }

    public function test_user_role_enum_cast(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertInstanceOf(UserRole::class, $user->user_role);
        $this->assertTrue($user->isAdmin());
    }
}
