<?php

namespace Tests\Unit;

use App\Events\MqttDataReceived;
use App\Models\MqttResponse;
use App\Services\Mqtt\MqttInboundMessageHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MqttInboundMessageHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected MqttInboundMessageHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(MqttInboundMessageHandler::class);
    }

    public function test_it_persists_and_broadcasts_valid_messages(): void
    {
        Event::fake([MqttDataReceived::class]);

        $result = $this->handler->handle(
            'dev-001/response',
            json_encode(['cmd' => 'statusUpdate', 'data' => ['voltage' => 230]])
        );

        $this->assertTrue($result);

        $this->assertDatabaseHas('mqtt_responses', [
            'device_id' => 'dev-001',
            'topic' => 'dev-001/response',
        ]);

        Event::assertDispatched(MqttDataReceived::class, function (MqttDataReceived $event) {
            return $event->deviceId === 'dev-001'
                && ($event->data['cmd'] ?? null) === 'statusUpdate';
        });
    }

    public function test_it_rejects_invalid_json(): void
    {
        Event::fake([MqttDataReceived::class]);

        $result = $this->handler->handle('dev-001/response', '{invalid-json');

        $this->assertFalse($result);
        $this->assertDatabaseCount('mqtt_responses', 0);
        Event::assertNotDispatched(MqttDataReceived::class);
    }

    public function test_it_rejects_invalid_topic(): void
    {
        Event::fake([MqttDataReceived::class]);

        $result = $this->handler->handle('/response', '{}');

        $this->assertFalse($result);
        $this->assertDatabaseCount('mqtt_responses', 0);
        Event::assertNotDispatched(MqttDataReceived::class);
    }

    public function test_extract_device_id_from_topic(): void
    {
        $this->assertSame('AA:BB:CC:DD:EE:FF', $this->handler->extractDeviceId('AA:BB:CC:DD:EE:FF/response'));
        $this->assertNull($this->handler->extractDeviceId('/response'));
    }
}
