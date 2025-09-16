<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Definir gates para los permisos
        Gate::define('users.list', function ($user) {
            return $user->can('users.list');
        });

        Gate::define('users.create', function ($user) {
            return $user->can('users.create');
        });

        Gate::define('users.edit', function ($user) {
            return $user->can('users.edit');
        });

        Gate::define('users.delete', function ($user) {
            return $user->can('users.delete');
        });

        Gate::define('users.update-status', function ($user) {
            return $user->can('users.update-status');
        });
    }
}
