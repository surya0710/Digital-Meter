<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Events\MqttDataReceived;
use App\Models\Devices;
use Illuminate\Support\Facades\Log;

class MqttSubscriber extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT device responses';

    public function handle()
    {
        $devices = Devices::select('device_id')->distinct()->get();

        if ($devices->isEmpty()) {
            $this->warn('No devices found to subscribe.');
            return;
        }

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

        foreach ($devices as $device) {
            if (!$device->device_id) {
                continue;
            }

            $topic = $device->device_id . '/response';

            $client->subscribe('+/response', function ($topic, $message) {

                // Log::info('MQTT MESSAGE RECEIVED', [
                //     'topic' => $topic,
                //     'raw'   => $message,
                // ]);

                $deviceId = trim(explode('/', $topic)[0]);

                $data = json_decode($message, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('INVALID JSON', ['message' => $message]);
                    return;
                }

                // Log::info('🔥 FIRING EVENT', [
                //     'device' => $deviceId,
                //     'data'   => $data,
                // ]);
                // echo "[$topic] $message\n";
                event(new \App\Events\MqttDataReceived($deviceId, $data));

            }, 0);
        }

        // 🔁 Keep listening forever
        $client->loop(true);
    }
}
