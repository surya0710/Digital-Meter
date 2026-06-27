<?php

namespace App\Console\Commands;

use App\Services\Mqtt\MqttConnectionFactory;
use App\Services\Mqtt\MqttInboundMessageHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\MqttClient;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';

    protected $description = 'Subscribe to MQTT device responses, persist telemetry, and broadcast via Reverb';

    public function handle(
        MqttConnectionFactory $connections,
        MqttInboundMessageHandler $handler
    ): int {
        if (! $connections->isConfigured()) {
            $this->error('MQTT broker is not configured. Set MQTT_HOST in your environment.');

            return Command::FAILURE;
        }

        $topic = $connections->subscribeTopic();
        $this->info("Listening for MQTT messages on {$topic}");

        while (true) {
            $client = null;

            try {
                $client = $connections->makeClient('sub');
                $this->connect($client, $connections);

                $client->subscribe(
                    $topic,
                    fn (string $receivedTopic, string $message) => $handler->handle($receivedTopic, $message),
                    $connections->subscribeQos()
                );

                $this->info('MQTT subscription active.');
                $client->loop(true);
            } catch (\Throwable $e) {
                Log::error('MQTT subscriber connection lost', [
                    'error' => $e->getMessage(),
                ]);

                $this->warn('Connection lost. Reconnecting in '.$connections->reconnectDelaySeconds().'s...');

                $this->disconnect($client);
                sleep($connections->reconnectDelaySeconds());
            }
        }
    }

    protected function connect(MqttClient $client, MqttConnectionFactory $connections): void
    {
        $client->connect($connections->settings(), true);
        $this->info('Connected to MQTT broker at '.$connections->connection()['host']);
    }

    protected function disconnect(?MqttClient $client): void
    {
        if (! $client) {
            return;
        }

        try {
            $client->disconnect();
        } catch (\Throwable) {
            // Ignore disconnect errors during recovery.
        }
    }
}
