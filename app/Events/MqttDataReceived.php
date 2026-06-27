<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MqttDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $deviceId,
        public readonly array $data,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(config('mqtt.broadcast_channel', 'device-dashboard'));
    }

    public function broadcastAs(): string
    {
        return config('mqtt.broadcast_event', 'mqtt.data');
    }

    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->deviceId,
            'data' => $this->data,
            'time' => now()->toDateTimeString(),
        ];
    }
}
