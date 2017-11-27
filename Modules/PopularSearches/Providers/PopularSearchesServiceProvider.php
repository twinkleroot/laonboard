<?php

namespace Modules\PopularSearches\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PopularSearchesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerPublic();
        $this->registerTables();

        // add popular config to configs table
        $data = ['del' => config('popularsearches.del')];
        addCustomConfig('popular', $data);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register the database tables.
     *
     * @return void
     */
    public function registerTables()
    {
        if(!\Schema::hasTable("populars")) {
            \Artisan::call("migrate");
        }
    }

    /**
     * Register public resources.
     *
     * @return void
     */
    protected function registerPublic()
    {
        $publicPath = public_path('modules/popularsearches');

        $sourcePath = __DIR__.'/../Public';

        $this->publishes([
            $sourcePath => $publicPath
        ], 'module-popularsearches-public');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('popularsearches.php'),
        ], 'module-popularsearches-config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'popularsearches'
        );
        mergeEvent(
            __DIR__.'/../Config/event.php', 'event'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/popularsearches');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'module-popularsearches-view');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/popularsearches';
        }, \Config::get('view.paths')), [$sourcePath]), 'popularsearches');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/popularsearches');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'popularsearches');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'popularsearches');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
