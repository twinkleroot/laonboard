<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ManageAuth;

class AdminMenu
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
        $user = auth()->user();
        $menus = config('menu');
        // 세션에 등록된 메뉴가 없으면
        if(!cache($user->id_hashkey.'_admin_primary_menu')) {
            $primaryMenu = [];
            $subMenu = [];
            if($user->isSuperAdmin()) { // 최고관리자일 때 모든 메뉴 가져오기
                foreach($menus as $key => $value) {
                    if( substr($key, -3) == '000') {
                        $primaryMenu[$key] = $value;
                    } else {
                        $subMenu[$key] = $value;
                    }
                }
            } else {    // 권한을 부여받은 관리자일 경우
                $manageMenus = ManageAuth::where(['user_id' => $user->id, 'isModule' => 0])->orderBy('menu')->get();
                foreach($manageMenus as $manageMenu) {
                    $subMenu[$manageMenu->menu] = $menus[$manageMenu->menu];
                    $primaryMenu[substr($manageMenu->menu, 0, 1). '00000'] = $menus[substr($manageMenu->menu, 0, 1). '00000'];
                }
            }

            // 캐시에 기록
            cache([$user->id_hashkey.'_admin_primary_menu' => $primaryMenu], 120);
            cache([$user->id_hashkey.'_admin_sub_menu' => $subMenu], 120);
        }

        return $next($request);
    }
}
