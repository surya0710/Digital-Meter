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
            Log::critical('❌ MQTT config missing');
            return Command::FAILURE;
        }

        // 🔁 Reconnect forever
        while (true) {

            try {
                Log::info('🔄 Creating MQTT client');

                $client = new MqttClient(
                    $connection['host'],
                    $connection['port'],
                    $connection['client_id'] . '_sub_' . uniqid()
                );

                $settings = (new ConnectionSettings)
                    ->setUsername($connection['username'])
                    ->setPassword($connection['password'])
                    ->setKeepAliveInterval(60)
                    ->setConnectTimeout(5)
                    ->setUseTls($connection['use_tls']);

                // 🔌 Connect
                $client->connect($settings, true);
                Log::info('✅ MQTT CONNECTED');

                // 📡 Subscribe
                $client->subscribe('+/response', function (string $topic, string $message) {

                    Log::info('📩 MQTT MESSAGE', compact('topic'));

                    $deviceId = explode('/', $topic)[0] ?? null;

                    if (!$deviceId) {
                        Log::warning('⚠️ Invalid topic', compact('topic'));
                        return;
                    }

                    $payload = json_decode($message, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::error('❌ Invalid JSON', [
                            'device' => $deviceId,
                            'raw'    => $message,
                        ]);
                        return;
                    }

                    event(new MqttDataReceived($deviceId, $payload));
                }, 0);

                // ♾️ Block until disconnect
                $client->loop(true);

            } catch (\Throwable $e) {

                Log::error('💥 MQTT CONNECTION LOST', [
                    'error' => $e->getMessage()
                ]);

                // 🧹 Clean shutdown
                try {
                    if (isset($client)) {
                        $client->disconnect();
                    }
                } catch (\Throwable) {
                    // ignore
                }

                sleep(5); // ⏳ backoff before reconnect
            }
        }
    }
}
