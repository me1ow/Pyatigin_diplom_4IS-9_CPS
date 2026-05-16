<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Gate для управления заданиями — доступен только user и admin
        Gate::define('submission-manage', function ($user) {
            return in_array($user->role, ['user', 'admin']);
        });

        // Gate для управления документами — загрузка и удаление (admin, expert)
        Gate::define('document-manage', function ($user) {
            return in_array($user->role, ['admin', 'expert']);
        });
    }
}
