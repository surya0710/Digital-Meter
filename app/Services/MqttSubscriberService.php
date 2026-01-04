<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\DataTransferException;

class MqttSubscriberService
{
    public function subscribe(array $topics): void
    {
        while (true) {
            try {
                $this->listen($topics);
            } catch (DataTransferException $e) {
                echo "Disconnected, retrying in 5s...\n";
                sleep(5);
            }
        }
    }

    private function listen(array $topics): void
    {
        $clientId = 'laravel_sub_' . uniqid();

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

        echo "MQTT subscriber connected\n";

        foreach ($topics as $topic => $qos) {
            $mqtt->subscribe($topic, function ($topic, $message) {
                echo "RECEIVED → {$topic}: {$message}\n";
            }, $qos);
        }

        while (true) {
            $mqtt->loopOnce(1000);
        }
    }
}
