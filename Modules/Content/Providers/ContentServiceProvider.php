<?php

namespace Modules\Content\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ContentServiceProvider extends ServiceProvider
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
        if(!\Schema::hasTable("contents")) {
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
        $publicPath = public_path('modules/content');

        $sourcePath = __DIR__.'/../Public';

        $this->publishes([
            $sourcePath => $publicPath
        ], 'module-content-public');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
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
        $viewPath = resource_path('views/modules/content');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'module-content-view');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/content';
        }, \Config::get('view.paths')), [$sourcePath]), 'content');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/content');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'content');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'content');
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
