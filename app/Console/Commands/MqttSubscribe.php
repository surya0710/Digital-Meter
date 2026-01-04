<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttSubscriberService;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT topics';

    public function handle(MqttSubscriberService $subscriber)
    {
        echo "Starting MQTT subscriber...\n";

        $subscriber->subscribe([
            'test/topic' => 0,
        ]);
    }
}
