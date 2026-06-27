<?php

namespace Tests\Unit;

use App\Enums\DeviceType;
use App\Models\Device;
use App\Models\User;
use App\Services\Device\DeviceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DeviceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DeviceService::class);
    }

    public function test_device_attributes_map_status_and_type(): void
    {
        $owner = User::factory()->create();

        $attributes = $this->service->deviceAttributesFromRequest([
            'user_id' => $owner->id,
            'device_id' => 'AA:BB:CC:DD:EE:FF',
            'device_name' => 'Panel A',
            'device_type' => DeviceType::Panel->value,
            'status' => '1',
        ]);

        $this->assertSame($owner->id, $attributes['user_id']);
        $this->assertTrue($attributes['is_active']);
        $this->assertSame(DeviceType::Panel, $attributes['device_type']);
    }

    public function test_legacy_energy_meter_id_auto_sets_type(): void
    {
        $legacyId = config('digital-meter.legacy_energy_meter_ids.0');
        $owner = User::factory()->create();

        $attributes = $this->service->deviceAttributesFromRequest([
            'user_id' => $owner->id,
            'device_id' => $legacyId,
            'status' => '1',
        ]);

        $this->assertSame(DeviceType::EnergyMeter, $attributes['device_type']);
    }

    public function test_non_admin_cannot_access_other_users_device(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $device = Device::factory()->create(['user_id' => $owner->id]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->service->findForDisplay($device->id, $intruder);
    }
}
