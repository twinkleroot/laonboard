<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Admin\Config;
use App\Admin\Menu;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 설정 캐시 등록
        $configNames = [
            'homepage', 'board', 'join', 'cert', 'email.default', 'email.board', 'email.join', 'theme', 'skin', 'sns', 'extra'
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
