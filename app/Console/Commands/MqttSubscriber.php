<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Events\MqttDataReceived;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT device responses';

    public function handle()
    {
        $connection = config('mqtt.connections.default');

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

        $client->connect($settings, true);

        // ✅ YOUR TOPIC
        $client->subscribe('C4:5B:BE:4F:02:3E/response', function ($topic, $message) {

            echo "[$topic] $message\n";

            $data = json_decode($message, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                event(new MqttDataReceived($topic, $data));
            }

        }, 0);

        $client->loop(true);
    }
}
