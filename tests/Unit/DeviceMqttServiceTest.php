<?php

namespace Tests\Unit;

use App\Services\Mqtt\DeviceMqttService;
use App\Services\MqttService;
use Mockery;
use Tests\TestCase;

class DeviceMqttServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_toggle_relay_inverts_state_before_publish(): void
    {
        $mqtt = Mockery::mock(MqttService::class);
        $mqtt->shouldReceive('publishToDevice')
            ->once()
            ->with('dev-1', Mockery::on(function (array $payload) {
                return $payload['cmd'] === 'setRelay'
                    && $payload['data']['relay'] === 3
                    && $payload['data']['state'] === 1;
            }))
            ->andReturn(true);

        $service = new DeviceMqttService($mqtt);

        $this->assertTrue($service->toggleRelay('dev-1', 3, 0));
    }

    public function test_fetch_voltage_calibration_publishes_two_commands(): void
    {
        $mqtt = Mockery::mock(MqttService::class);
        $mqtt->shouldReceive('publishToDevice')
            ->once()
            ->with('dev-2', Mockery::on(fn ($p) => $p['cmd'] === 'getVoltageCalibration'))
            ->andReturn(true);
        $mqtt->shouldReceive('publishToDevice')
            ->once()
            ->with('dev-2', Mockery::on(fn ($p) => $p['cmd'] === 'getVoltageLimits'))
            ->andReturn(true);

        $service = new DeviceMqttService($mqtt);

        $this->assertTrue($service->fetchVoltageCalibration('dev-2'));
    }

    public function test_create_timer_delegates_formatted_payload(): void
    {
        $mqtt = Mockery::mock(MqttService::class);
        $mqtt->shouldReceive('publishToDevice')
            ->once()
            ->with('dev-3', Mockery::on(function (array $payload) {
                return $payload['cmd'] === 'createTimer'
                    && $payload['data']['startTime'] === '09:30:00'
                    && $payload['data']['endTime'] === '18:00:00';
            }))
            ->andReturn(true);

        $service = new DeviceMqttService($mqtt);

        $this->assertTrue($service->createTimer('dev-3', 1, [1], '09:30:00', '18:00:00', 1));
    }
}
