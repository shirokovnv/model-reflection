<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ModelReflectionServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
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
}
