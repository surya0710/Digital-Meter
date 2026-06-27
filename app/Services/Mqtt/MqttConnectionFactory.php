<?php

namespace App\Services\Mqtt;

use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttConnectionFactory
{
    protected array $connection;

    public function __construct()
    {
        $this->connection = config('mqtt.connections.default', []);
    }

    public function connection(): array
    {
        return $this->connection;
    }

    public function isConfigured(): bool
    {
        return ! empty($this->connection['host']);
    }

    public function makeClient(string $suffix): MqttClient
    {
        return new MqttClient(
            $this->connection['host'],
            (int) $this->connection['port'],
            $this->connection['client_id'].'_'.$suffix.'_'.uniqid()
        );
    }

    public function settings(): ConnectionSettings
    {
        return (new ConnectionSettings)
            ->setUsername($this->connection['username'] ?? null)
            ->setPassword($this->connection['password'] ?? null)
            ->setKeepAliveInterval(60)
            ->setConnectTimeout((int) ($this->connection['timeout'] ?? 5))
            ->setUseTls((bool) ($this->connection['use_tls'] ?? false));
    }

    public function subscribeTopic(): string
    {
        return config('mqtt.subscribe_topic', '+/response');
    }

    public function subscribeQos(): int
    {
        return (int) config('mqtt.quality_of_service', 0);
    }

    public function reconnectDelaySeconds(): int
    {
        return (int) config('mqtt.reconnect_delay', 5);
    }
}
