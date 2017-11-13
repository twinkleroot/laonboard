<?php

namespace Modules\Certify\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use App\Models\Config;
use Cache;

class CertifyServiceProvider extends ServiceProvider
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
        // cert 설정 캐시 등록
        $this->cacheCertConfig();
        // 미들웨어 등록
        $this->registerMiddleWare();
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
        if(!\Schema::hasTable("cert_history")) {
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
        $publicPath = public_path('modules/certify');

        $sourcePath = __DIR__.'/../Public';

        $this->publishes([
            $sourcePath => $publicPath
        ], 'module-certify-public');
    }

    /**
     * Register middleware.
     *
     * @return void
     */
    public function registerMiddleWare()
    {
        // 본인확인과 성인인증 여부로 글 목록보기, 읽기, 쓰기, 수정, 답변이 가능한지 검사
        $this->app['router']->aliasMiddleware('cert', \Modules\Certify\Http\Middleware\CheckCert::class);
        $this->addCertMiddleware();
    }

    // 게시판 해당 라우트에 cert 미들웨어 추가
    private function addCertMiddleware()
    {
        $routeNames = [
            'store',
            'index',
            'create',
            'edit',
            'create.reply',
            'update',
            'view',
            'download',
            'link',
        ];
        foreach($routeNames as $routeName) {
            // 라우트 이름마다 cert 미들웨어 추가
            array_push(app()['routes']->getByName("board.$routeName")->action['middleware'], 'cert');
        }
    }

    /**
     * Config register cache.
     *
     * @return void
     */
    public function cacheCertConfig()
    {
        $cert = Config::where('name', 'config.cert')->first();
        if(!$cert) {
            $configModel = new Config();
            $defaultCertConfigs = [
                'certUse' => config('certify.certUse'),
                'certHp' => config('certify.certHp'),
                'certKcbCd' => config('certify.certKcbCd'),
                'certLimit' => config('certify.certLimit'),
                'certReq' => config('certify.certReq'),
            ];
            $configCert = $configModel->createConfig('config.cert', $defaultCertConfigs);
        } else {
            Cache::forever("config.cert", json_decode($cert->vars));
        }
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('certify.php'),
        ], 'module-certify-config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'certify'
        );
        mergeEvent(
            __DIR__.'/../Config/event.php', 'event', 1
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/certify');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'module-certify-view');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/certify';
        }, \Config::get('view.paths')), [$sourcePath]), 'certify');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/certify');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'certify');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'certify');
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
