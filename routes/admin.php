<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('contrasena', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('contrasena', [PasswordController::class, 'update'])->name('password.update');

    Route::get('ajustes', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('ajustes', [SettingsController::class, 'update'])->name('settings.update');
});
