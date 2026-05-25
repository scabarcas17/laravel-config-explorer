<?php

declare(strict_types=1);

namespace Scabarcas\LaravelConfigExplorer;

use Illuminate\Support\ServiceProvider;

class ConfigExplorerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config-explorer.php', 'config-explorer');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'config-explorer');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config-explorer.php' => config_path('config-explorer.php'),
            ], 'config-explorer-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/config-explorer'),
            ], 'config-explorer-views');
        }
    }
}
