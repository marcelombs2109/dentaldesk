<?php

namespace App\Providers;

use App\Services\CitaService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CitaService::class, function () {
            return new CitaService();
        });
    }

    public function boot(): void
    {
        //
        \Carbon\Carbon::setLocale('es');
    }
}