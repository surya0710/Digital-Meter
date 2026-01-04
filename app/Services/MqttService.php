<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function publish(string $topic, string $message): void
    {
        $clientId = 'laravel_pub_' . uniqid();

        $settings = (new ConnectionSettings)
            ->setUsername(config('mqtt.username'))
            ->setPassword(config('mqtt.password'))
            ->setUseTls(true)
            ->setTlsCertificateAuthorityFile(
                'C:\xampp\php\extras\ssl\cacert.pem'
            )
            ->setKeepAliveInterval(30);

        $mqtt = new MqttClient(
            config('mqtt.host'),
            (int) config('mqtt.port'),
            $clientId
        );

        $mqtt->connect($settings, true);
        $mqtt->publish($topic, $message, 0);
        $mqtt->disconnect();
    }
}
