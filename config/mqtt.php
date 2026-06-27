<?php

return [

    'default_connection' => 'default',

    'connections' => [

        'default' => [
            'host' => env('MQTT_HOST'),
            'port' => env('MQTT_PORT', 1883),
            'username' => env('MQTT_USERNAME'),
            'password' => env('MQTT_PASSWORD'),

            // MUST be unique
            'client_id' => env('MQTT_CLIENT_ID', 'laravel_' . uniqid()),

            'clean_session' => true,
            'protocol' => 'mqtt',
            'quality_of_service' => 0,
            'use_tls' => false,
            'timeout' => 5,
        ],
    ],

    'subscribe_topic' => env('MQTT_SUBSCRIBE_TOPIC', '+/response'),

    'reconnect_delay' => env('MQTT_RECONNECT_DELAY', 5),

    'broadcast_channel' => env('MQTT_BROADCAST_CHANNEL', 'device-dashboard'),

    'broadcast_event' => env('MQTT_BROADCAST_EVENT', 'mqtt.data'),
];
