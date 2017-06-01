<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// 설정 캐시에 저장
// 홈페이지 기본환경 설정
$homepageConfig = Cache::rememberForever("config.homepage", function() {
                        $config = new App\Config(); // 캐시에 저장할 때만 객체 생성
                        $hConfig = App\Config::where('name', 'config.homepage')->first();
                        if(is_null($hConfig)) {
                            $hConfig = $config->createConfigHomepage();
                        }
                        return $config->pullConfig($hConfig);
                    });
$boardConfig = Cache::rememberForever("config.board", function() {
                        $config = new App\Config(); // 캐시에 저장할 때만 객체 생성
                        $bConfig = App\Config::where('name', 'config.board')->first();
                        if(is_null($bConfig)) {
                            $bConfig = $config->createConfigBoard();
                        }
                        return $config->pullConfig($bConfig);
                    });
$joinConfig = Cache::rememberForever("config.join", function() {
                        $config = new App\Config(); // 캐시에 저장할 때만 객체 생성
                        $jConfig = App\Config::where('name', 'config.join')->first();
                        if(is_null($jConfig)) {
                            $jConfig = $config->createConfigJoin();
                        }
                        return $config->pullConfig($jConfig);
                    });

$response->send();

$kernel->terminate($request, $response);
