<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Support\Settings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::resourceVerbs(['create' => 'nuevo', 'edit' => 'editar']);

        View::composer('layouts.admin', function ($view) {
            $view->with('unreadMessages', auth()->check() ? ContactMessage::unread()->count() : 0);
        });
    }
}
