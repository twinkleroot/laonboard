<?php

namespace Modules\Inform\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class InformServiceProvider extends ServiceProvider
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

        // add inform config to configs table
        $data = ['del' => config('inform.del')];
        addCustomConfig('inform', $data);
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
        if(!\Schema::hasTable("notifications")) {
            \Artisan::call("migrate");
        }
    }

    /**
     * Register public resources.
     *
     * @return void
     */
    public function registerPublic()
    {
        $publicPath = public_path('modules/inform');

        $sourcePath = __DIR__.'/../Public';

        $this->publishes([
            $sourcePath => $publicPath
        ], 'module-inform-public');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('inform.php'),
        ], 'module-inform-config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'inform'
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
        $viewPath = resource_path('views/modules/inform');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'module-inform-view');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/inform';
        }, \Config::get('view.paths')), [$sourcePath]), 'inform');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/inform');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'inform');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'inform');
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
