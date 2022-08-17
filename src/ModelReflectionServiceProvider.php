<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ModelReflectionServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/model-reflection.php', 'model-reflection');

        // Register the service the package provides.
        $this->app->singleton('model-reflection', function ($app) {
            return new ModelReflection(DB::connection());
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['model-reflection'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/model-reflection.php' => config_path('model-reflection.php'),
        ], 'model-reflection.config');
    }
}
