<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MqttDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $deviceId;
    public array $data;

    public function __construct(string $deviceId, array $data)
    {
        $this->deviceId = $deviceId;
        $this->data     = $data;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('device.' . $this->deviceId);
    }

    public function broadcastAs(): string
    {
        return 'mqtt.data';
    }

    public function broadcastWith(): array
    {
        // Log::info('🔥 Broadcasting MQTT', [
        //     'device' => $this->deviceId,
        //     'payload' => $this->data,
        // ]);

        return [
            'device_id' => $this->deviceId,
            'data'      => $this->data,
            'time'      => now()->toDateTimeString(),
        ];
    }
}
