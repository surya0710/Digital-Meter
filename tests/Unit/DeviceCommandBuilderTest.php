<?php

namespace Tests\Unit;

use App\Services\Mqtt\DeviceCommandBuilder;
use Tests\TestCase;

class DeviceCommandBuilderTest extends TestCase
{
    public function test_set_relay_builds_expected_payload(): void
    {
        $payload = DeviceCommandBuilder::setRelay(2, 1, 'msg-1');

        $this->assertSame([
            'msgId' => 'msg-1',
            'cmd' => 'setRelay',
            'data' => ['relay' => 2, 'state' => 1],
        ], $payload);
    }

    public function test_create_timer_normalizes_time_fields(): void
    {
        $payload = DeviceCommandBuilder::createTimer(
            relay: 0,
            days: [1, 2, 3],
            startTime: '08:00:00',
            endTime: '17:00:00',
            enabled: true
        );

        $this->assertSame('createTimer', $payload['cmd']);
        $this->assertSame('08:00:00', $payload['data']['startTime']);
        $this->assertSame('17:00:00', $payload['data']['endTime']);
    }

    public function test_shutdown_all_builds_set_all_relays_command(): void
    {
        $payload = DeviceCommandBuilder::setAllRelays(0);

        $this->assertSame('setAllRelays', $payload['cmd']);
        $this->assertSame(0, $payload['data']['state']);
        $this->assertSame('all_off', $payload['msgId']);
    }

    public function test_voltage_protection_includes_min_and_max(): void
    {
        $payload = DeviceCommandBuilder::setVoltageProtection(180, 260);

        $this->assertSame('setVoltageProtection', $payload['cmd']);
        $this->assertSame(180, $payload['data']['min']);
        $this->assertSame(260, $payload['data']['max']);
    }
}
