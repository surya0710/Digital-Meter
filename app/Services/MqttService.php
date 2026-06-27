<?php

namespace App\Services;

use App\Services\Mqtt\MqttConnectionFactory;
use Illuminate\Support\Facades\Log;

class MqttService
{
    public function __construct(
        protected MqttConnectionFactory $connections
    ) {}

    public function publishToDevice(string $deviceId, array $payload, int $qos = 0, bool $retain = false): bool
    {
        return $this->publish("{$deviceId}/request", $payload, $qos, $retain);
    }

    public function publish(string $topic, array|string $message, int $qos = 0, bool $retain = false): bool
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }

        if (! $this->connections->isConfigured()) {
            Log::error('MQTT publish failed: broker not configured', compact('topic'));

            return false;
        }

        try {
            $client = $this->connections->makeClient('pub');
            $client->connect($this->connections->settings(), true);
            $client->publish($topic, $message, $qos, $retain);
            $client->disconnect();

            return true;
        } catch (\Throwable $e) {
            Log::error('MQTT publish failed', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
