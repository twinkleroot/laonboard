<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Models\Config;
use App\Models\Menu;

class ConfigRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 설정 캐시 등록
        $configNames = [
            'homepage', 'board', 'join', 'email.default', 'email.board', 'email.join', 'theme', 'skin', 'sns', 'extra'
        ];
        foreach($configNames as $configName) {
            $this->registerConfigCache($configName);
        }

        // 메뉴바 설정 가져오기
        $menuList = Cache::rememberForever("menuList", function() {
            $menu = new Menu(); // 캐시에 저장할 때만 객체 생성
            return $menu->getMainMenu();
        });
        Cache::rememberForever("subMenuList", function() use($menuList){
            $menu = new Menu(); // 캐시에 저장할 때만 객체 생성
            return $menu->getSubMenuList($menuList);
        });

        return $next($request);
    }

    private function registerConfigCache($configName)
    {
        if(!Cache::has("config.$configName")) {
            Cache::forever("config.$configName", $this->getConfig($configName));
        }
    }

    private function getConfig($configName)
    {
        $configModel = new Config(); // 캐시에 저장할 때만 객체 생성
        $config = Config::where('name', 'config.'. $configName)->first();
        if(is_null($config)) {
            $config = $configModel->createConfigController($configName);
        }
        return $configModel->pullConfig($config);
    }
}
