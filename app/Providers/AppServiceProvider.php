<?php

namespace App\Providers;

use App\Models\User;
use App\Services\CitaService;
use Illuminate\Support\Facades\Gate;
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
        \Carbon\Carbon::setLocale('es');

        // SDD-02 R2/R3: un Gate por permiso, definido desde config/permisos.php.
        foreach (config('permisos', []) as $permiso => $roles) {
            Gate::define($permiso, function (User $user) use ($roles): bool {
                return in_array($user->rol, $roles, true);
            });
        }
    }
}
