<?php

use App\Http\Controllers\DeviceMqttCommandController;
use Illuminate\Support\Facades\Route;

Route::prefix('devices')
    ->name('devices.')
    ->middleware('throttle:device-commands')
    ->group(function () {
        Route::post('/switch', [DeviceMqttCommandController::class, 'switch'])->name('switch');
        Route::post('/fetchTimer', [DeviceMqttCommandController::class, 'fetchTimer'])->name('fetchTimer');
        Route::post('/deleteTimer', [DeviceMqttCommandController::class, 'deleteTimer'])->name('deleteTimer');
        Route::post('/createTimer', [DeviceMqttCommandController::class, 'createTimer'])->name('saveTimer');
        Route::post('/shutdownAll', [DeviceMqttCommandController::class, 'shutdownAll'])->name('shutdownAll');
        Route::post('/setRefreshRate', [DeviceMqttCommandController::class, 'setRefreshRate'])->name('setRefreshRate');
        Route::post('/fetchMemory', [DeviceMqttCommandController::class, 'fetchMemory'])->name('fetchMemory');
        Route::post('/fetchRefreshRate', [DeviceMqttCommandController::class, 'fetchRefreshRate'])->name('getRefreshRate');
        Route::post('/getCurrentLimit', [DeviceMqttCommandController::class, 'getCurrentLimit'])->name('getCurrentLimit');
        Route::post('/fetchVoltageCalibration', [DeviceMqttCommandController::class, 'fetchVoltageCalibration'])->name('getVoltageCalibration');
        Route::post('/setCalibratedVoltage', [DeviceMqttCommandController::class, 'setCalibratedVoltage'])->name('setCalibratedVoltage');
        Route::post('/setCalibratedCurrent', [DeviceMqttCommandController::class, 'setCalibratedCurrent'])->name('setCalibratedCurrent');
        Route::post('/{device}/setVoltageProtection', [DeviceMqttCommandController::class, 'setVoltageProtection'])
            ->whereNumber('device')
            ->name('setVoltageProtection');
        Route::post('/{device}/setCurrentProtection', [DeviceMqttCommandController::class, 'setCurrentProtection'])
            ->whereNumber('device')
            ->name('setCurrentProtection');
    });
