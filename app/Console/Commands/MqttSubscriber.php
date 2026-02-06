<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Events\MqttDataReceived;
use Illuminate\Support\Facades\Log;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT device responses and broadcast via Reverb';

    public function handle()
    {
        $connection = config('mqtt.connections.default');

        if (!$connection) {
            $this->error('MQTT connection config not found.');
            return;
        }

        $client = new MqttClient(
            $connection['host'],
            $connection['port'],
            $connection['client_id'] . '_sub'
        );

        $settings = (new ConnectionSettings)
            ->setUsername($connection['username'])
            ->setPassword($connection['password'])
            ->setKeepAliveInterval(60)
            ->setUseTls($connection['use_tls']);

        try {
            $client->connect($settings, true);
        } catch (\Throwable $e) {
            Log::error('❌ MQTT CONNECT FAILED', ['error' => $e->getMessage()]);
            $this->error('MQTT connection failed.');
            return;
        }

        Log::info('✅ MQTT CONNECTED');

        /**
         * 🔥 Subscribe ONCE using wildcard
         * Example topics:
         *   DEVICE123/response
         *   DEVICE456/response
         */
        $client->subscribe('+/response', function (string $topic, string $message) {

            Log::info('📡 MQTT MESSAGE RECEIVED', [
                'topic' => $topic,
                'message' => $message,
            ]);

            /**
             * Extract device ID from topic
             * DEVICE123/response → DEVICE123
             */
            $parts = explode('/', $topic);
            $deviceId = $parts[0] ?? null;

            if (!$deviceId) {
                Log::warning('⚠️ Invalid topic format', ['topic' => $topic]);
                return;
            }

            $payload = json_decode($message, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('❌ INVALID JSON PAYLOAD', [
                    'device' => $deviceId,
                    'raw' => $message,
                ]);
                return;
            }

            Log::info('🚀 BROADCASTING EVENT', [
                'device' => $deviceId,
                'payload' => $payload,
            ]);

            /**
             * 🔥 Broadcast to Reverb
             */
            event(new MqttDataReceived($deviceId, $payload));

        }, 0);

        /**
         * 🔁 Keep the process alive forever
         */
        while (true) {
            try {
                $client->loop(true);
            } catch (\Throwable $e) {
                Log::error('❌ MQTT LOOP ERROR', ['error' => $e->getMessage()]);
                sleep(5);
            }
        }
    }
}
