<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TestimonialController;
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

    Route::resource('posts', PostController::class)->except('show')->scoped(['post' => 'id']);
    Route::resource('sucursales', BranchController::class)->except('show')->names('branches')->parameters(['sucursales' => 'id']);
    Route::resource('portadas', HeroSlideController::class)->except('show')->names('slides')->parameters(['portadas' => 'id']);
    Route::resource('servicios', ServiceController::class)->except('show')->names('services')->parameters(['servicios' => 'id']);
    Route::resource('preguntas', FaqController::class)->except('show')->names('faqs')->parameters(['preguntas' => 'id']);
    Route::resource('testimonios', TestimonialController::class)->except('show')->names('testimonials')->parameters(['testimonios' => 'id']);

    Route::get('mensajes', [MessageController::class, 'index'])->name('messages.index');
    Route::get('mensajes/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::patch('mensajes/{message}/atendido', [MessageController::class, 'toggle'])->name('messages.toggle');
    Route::delete('mensajes/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
});
