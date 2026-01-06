<?php

return [

    'default_connection' => 'default',

    'connections' => [

        'default' => [
            'host' => env('MQTT_HOST'),
            'port' => 1883,
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
];
