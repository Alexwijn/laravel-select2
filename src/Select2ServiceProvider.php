<?php

namespace Alexwijn\Select2;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Alexwijn\Select2\Select2ServiceProvider
 */
class Select2ServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->setupAssets();

        $this->app->singleton('select2', Select2::class);
        $this->app->singleton('select2.html', Html\Builder::class);

        Blade::directive('dropdown', $this->app->make(CompileDropdownDirective::class));
    }

    /**
     * Boot the instance, add macros for select2 engines.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Setup package assets.
     *
     * @return void
     */
    protected function setupAssets(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'select2');
        $this->mergeConfigFrom($config = __DIR__ . '/../config/select2.php', 'select2');

        if ($this->app->runningInConsole()) {
            $this->publishes([$config => config_path('select2.php')], 'select2');
        }
    }
}
