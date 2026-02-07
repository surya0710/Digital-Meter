<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MqttController;
use App\Livewire\DeviceControl;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::group(['prefix' => 'dashboard','middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/users/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/users/create', [UserController::class, 'createForm'])->name('users.createform');
    Route::post('/users/create', [UserController::class, 'create'])->name('users.create');

    Route::get('/devices/list', [DeviceController::class, 'list'])->name('devices.list');
    Route::get('/devices/create', [DeviceController::class, 'createForm'])->name('devices.createform');
    Route::post('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::get('/devices/{id}/view', [DeviceController::class, 'view'])->name('devices.view');

    Route::get('/devices/publish', [DeviceController::class, 'publish'])->name('devices.publish');
    Route::post('/mqtt/publish', [MqttController::class, 'publish']);

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::post('/device/switch', [DeviceController::class, 'switch'])->name('devices.switch');
Route::post('/devices/mqtt-data', [DeviceController::class, 'getMqttData'])->name('devices.mqtt.data');
Route::post('devices/fetchTimer', [DeviceController::class, 'fetchTimer'])->name('devices.fetchTimer');
Route::post('devices/deleteTimer', [DeviceController::class, 'deleteTimer'])->name('devices.deleteTimer');
Route::post('devices/createTimer', [DeviceController::class, 'createTimer'])->name('devices.saveTimer');
Route::post('devices/shutdownAll', [DeviceController::class, 'shutdownAll'])->name('devices.shutdownAll');
Route::post('devices/setRefreshRate', [DeviceController::class, 'setRefreshRate'])->name('devices.setRefreshRate');
Route::post('devices/fetchMemory', [DeviceController::class, 'fetchMemory'])->name('devices.fetchMemory');
Route::post('devices/fetchRefreshRate', [DeviceController::class, 'fetchRefreshRate'])->name('devices.getRefreshRate');
Route::post('devices/fetchVoltageCalibration', [DeviceController::class, 'fetchVoltageCalibration'])->name('devices.getVoltageCalibration');
Route::post('devices/setCalibratedVoltage', [DeviceController::class, 'setCalibratedVoltage'])->name('devices.setCalibratedVoltage');
Route::post('devices/setCalibratedCurrent', [DeviceController::class, 'setCalibratedCurrent'])->name('devices.setCalibratedCurrent');
Route::post('/devices/{id}/updateSwitchName', [DeviceController::class, 'updateSwitchName'])->name('devices.updateSwitchName');

