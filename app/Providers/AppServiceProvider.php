<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shift;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bagikan data $activeShift ke seluruh file Blade view secara global
        View::composer('*', function ($view) {
            $activeShift = Shift::getActiveShift(); // Ambil shift yang berstatus 'open'
            $view->with('activeShift', $activeShift);
        });
    }
}