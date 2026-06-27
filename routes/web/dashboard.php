<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('dashboard.full')->group(function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
});
