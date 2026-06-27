<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->prefix('dashboard')->group(function () {
    require __DIR__.'/web/dashboard.php';
    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/devices.php';
});

Route::middleware(['auth', 'active'])->group(function () {
    require __DIR__.'/web/device-mqtt.php';
});

require __DIR__.'/web/auth.php';
