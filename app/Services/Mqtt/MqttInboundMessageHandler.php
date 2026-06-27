<?php

namespace App\Services\Mqtt;

use App\Events\MqttDataReceived;
use App\Models\MqttResponse;
use Illuminate\Support\Facades\Log;

class MqttInboundMessageHandler
{
    public function handle(string $topic, string $message): bool
    {
        $deviceId = $this->extractDeviceId($topic);

        if (! $deviceId) {
            Log::warning('MQTT message ignored: invalid topic format', compact('topic'));

            return false;
        }

        $payload = json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('MQTT message ignored: invalid JSON payload', [
                'device_id' => $deviceId,
                'topic' => $topic,
                'raw' => $message,
            ]);

            return false;
        }

        MqttResponse::create([
            'device_id' => $deviceId,
            'topic' => $topic,
            'message' => $payload,
            'received_at' => now(),
        ]);

        event(new MqttDataReceived($deviceId, $payload));

        return true;
    }

    public function extractDeviceId(string $topic): ?string
    {
        $deviceId = explode('/', $topic)[0] ?? null;

        return $deviceId !== '' ? $deviceId : null;
    }
}
