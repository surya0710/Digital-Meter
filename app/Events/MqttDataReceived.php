<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MqttDataReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * MQTT topic
     */
    public string $topic;

    /**
     * Decoded MQTT payload
     */
    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(string $topic, array $data)
    {
        $this->topic = $topic;
        $this->data  = $data;
    }

    /**
     * Channel the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        // Public channel (simple & works out of the box)
        return new Channel('device-dashboard');
    }

    /**
     * Optional: Custom event name on frontend
     */
    public function broadcastAs(): string
    {
        return 'mqtt.data.received';
    }

    /**
     * Data sent to frontend
     */
    public function broadcastWith(): array
    {
        return [
            'topic' => $this->topic,
            'data'  => $this->data,
            'time'  => now()->toDateTimeString(),
        ];
    }
}
