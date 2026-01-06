<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

class MqttService
{
    protected $connection;

    public function __construct()
    {
        $this->connection = config('mqtt.connections.default');
    }

    public function publish($topic, $message, $qos = 0, $retain = false)
    {
        try {
            $client = new MqttClient(
                $this->connection['host'],
                $this->connection['port'],
                $this->connection['client_id'] . '_pub'
            );

            $settings = (new ConnectionSettings)
                ->setUsername($this->connection['username'])
                ->setPassword($this->connection['password'])
                ->setKeepAliveInterval(60)
                ->setUseTls($this->connection['use_tls']);

            $client->connect($settings, true);
            $client->publish($topic, $message, $qos, $retain);
            $client->disconnect();

            return true;

        } catch (\Throwable $e) {
            Log::error('MQTT Publish Error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
