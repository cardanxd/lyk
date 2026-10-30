<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('nosotros', [PageController::class, 'about'])->name('about');
Route::get('aviso-de-privacidad', [PageController::class, 'privacy'])->name('privacy');
Route::get('contacto', [ContactController::class, 'create'])->name('contact');
Route::post('contacto', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
