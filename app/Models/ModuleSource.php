<?php

namespace App\Models;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Module;
use Artisan;

class ModuleSource
{
    // 모듈 목록
    public function getIndexParams($request)
    {
        $keyword = $request->filled('keyword') ? strtolower($request->keyword) : '';
        $use = $request->filled('use') ? $request->use : '';
        $enableModules = Module::enabled();
        $disableModules = Module::disabled();
        if($keyword) {
            $results = [];
            $modules = array_collapse([$enableModules, $disableModules]);
            foreach($modules as $module) {
                if(strpos($module->getLowerName(), $keyword) !== false) {
                    $results[$module->getName()] = $module;
                }
            }
        } else {
            // 활성화된 모듈 정렬
            $enableModules = array_sort($enableModules, function($value) {
                return $value;
            });
            // 비활성화된 모듈 정렬
            $disableModules = array_sort($disableModules, function($value) {
                return $value;
            });
            // 모듈 목록 합치기
            $results = array_collapse([$enableModules, $disableModules]);
        }

        foreach($results as $module) {
            if($use == 'yes' && $module->disabled()) {
                $results = array_except($results, $module->getName());
            } else if($use == 'no' && $module->enabled()){
                $results = array_except($results, $module->getName());
            }
        }

        return [
            'modules' => $results,
            'keyword' => $request->filled('keyword') ? $request->keyword : '',
        ];
    }

    // 모듈 상세 보기
    public function getShowParams($name)
    {
        $module = Module::find($name);

        return [
            'module' => $module,
        ];
    }

    public function getEditParams($name)
    {
        $module = Module::find($name);

        return [
            'module' => $module,
        ];
    }

    // 모듈 삭제
    public function destroyModule($request)
    {
        foreach((array) $request->moduleName as $name) {
            Module::find($name)->delete();
        }

        // 캐시 초기화
        Artisan::call("cache:clear");
    }

    // 모듈 활성화
    public function activeModule($request)
    {
        foreach((array) $request->moduleName as $name) {
            // 활성화
            Module::find($name)->enable();
            // view, resource 설치
            $this->publishResource($name);
        }
    }

    // 모듈 비활성화
    public function inactiveModule($request)
    {
        foreach((array) $request->moduleName as $name) {
            Module::find($name)->disable();
            $this->deleteManageAuth($name);
        }

        // 캐시 초기화
        Artisan::call("cache:clear");
    }

    // view, resource 설치
    private function publishResource($name)
    {
        $public = "module-$name-public";
        $view = "module-$name-view";
        $config = "module-$name-config";

        try {
            Artisan::call("vendor:publish", ['--tag' => $public]);
            Artisan::call("vendor:publish", ['--tag' => $view]);
            Artisan::call("vendor:publish", ['--tag' => $config]);
        } catch(\Symfony\Component\Console\Exception\CommandNotFoundException $e) {
            // ignore
        }
    }

    // 부여된 모듈 관리권한 모두 삭제
    private function deleteManageAuth($name)
    {
        ManageAuth::where('menu', $name)->delete();
    }
}
