<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use App\Services\System\SystemUpdateService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $hasUpdate = Cache::remember('has_system_update', 3600, function () {
                return app(SystemUpdateService::class)->checkForUpdates() !== null;
            });

            $view->with('hasUpdate', $hasUpdate);
        });
    }
}
