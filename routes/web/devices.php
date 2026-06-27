<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('devices')->name('devices.')->group(function () {
    Route::get('/list', [DeviceController::class, 'list'])->name('list');

    Route::get('/{device}/view', [DeviceController::class, 'view'])
        ->whereNumber('device')
        ->name('view');

    Route::post('/{device}/updateSwitchName', [DeviceController::class, 'updateSwitchName'])
        ->whereNumber('device')
        ->name('updateSwitchName');

    Route::post('/mqtt-data', [DeviceController::class, 'getMqttData'])->name('mqtt.data');
});
