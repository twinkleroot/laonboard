<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Admin\Config;
use App\Admin\Menu;

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
        // 홈페이지 기본환경 설정
        if(!Cache::has('config.homepage')) {
            Cache::forever('config.homepage', $this->registerConfigCache('homepage'));
        }
        // 회원 가입 설정
        if(!Cache::has('config.join')) {
            Cache::forever('config.join', $this->registerConfigCache('join'));
        }
        // 게시판 기본 설정
        if(!Cache::has('config.board')) {
            Cache::forever('config.board', $this->registerConfigCache('board'));
        }
        // 기본 메일 환경 설정
        if(!Cache::has('config.email.default')) {
            Cache::forever('config.email.default', $this->registerConfigCache('email.default'));
        }
        // 게시판 글 작성시 메일 설정
        if(!Cache::has('config.email.board')) {
            Cache::forever('config.email.board', $this->registerConfigCache('email.board'));
        }
        // 회원가입 시 메일 설정
        if(!Cache::has('config.email.join')) {
            Cache::forever('config.email.join', $this->registerConfigCache('email.join'));
        }
        // 테마 설정
        if(!Cache::has('config.theme')) {
            Cache::forever('config.theme', $this->registerConfigCache('theme'));
        }
        // 개별 스킨 설정
        if(!Cache::has('config.skin')) {
            Cache::forever('config.skin', $this->registerConfigCache('skin'));
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
        $configModel = new Config(); // 캐시에 저장할 때만 객체 생성
        $config = Config::where('name', 'config.'. $configName)->first();
        if(is_null($config)) {
            $config = $configModel->createConfigController($configName);
        }
        return $configModel->pullConfig($config);
    }
}
