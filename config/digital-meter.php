<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Customer-only dashboard access
    |--------------------------------------------------------------------------
    |
    | Users with this email get a simplified layout (no sidebar) and are
    | redirected straight to their assigned device after login.
    |
    */
    'customer_email' => env('CUSTOMER_EMAIL', 'skelectricals@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Legacy device IDs
    |--------------------------------------------------------------------------
    |
    | Device IDs that were treated as energy meters before device_type existed.
    |
    */
    'legacy_energy_meter_ids' => [
        '3C:E9:0E:CD:90:45',
    ],

];
