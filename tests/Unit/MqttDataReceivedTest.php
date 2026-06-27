<?php

namespace Tests\Unit;

use App\Events\MqttDataReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MqttDataReceivedTest extends TestCase
{
    use RefreshDatabase;

    public function test_broadcast_metadata_uses_configured_channel_and_event_name(): void
    {
        config([
            'mqtt.broadcast_channel' => 'device-dashboard',
            'mqtt.broadcast_event' => 'mqtt.data',
        ]);

        $event = new MqttDataReceived('dev-77', ['cmd' => 'getRate', 'data' => ['rate' => 10]]);

        $this->assertSame('private-device-dashboard', $event->broadcastOn()->name);
        $this->assertSame('mqtt.data', $event->broadcastAs());
        $this->assertSame([
            'device_id' => 'dev-77',
            'data' => ['cmd' => 'getRate', 'data' => ['rate' => 10]],
            'time' => $event->broadcastWith()['time'],
        ], $event->broadcastWith());
    }
}
