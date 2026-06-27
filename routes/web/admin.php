<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MqttController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin')->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/list', [UserController::class, 'list'])->name('list');
        Route::get('/create', [UserController::class, 'createForm'])->name('createform');
        Route::post('/create', [UserController::class, 'create'])->name('create');
    });

    Route::prefix('devices')->name('devices.')->group(function () {
        Route::get('/create', [DeviceController::class, 'createForm'])->name('createform');
        Route::post('/create', [DeviceController::class, 'create'])->name('create');
    });

    Route::post('/mqtt/publish', [MqttController::class, 'publish'])->name('mqtt.publish');
});
