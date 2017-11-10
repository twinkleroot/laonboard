<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use DB;
use Module;

class ManageAuth extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'manage_auth';
    }

    public function getIndexParams($request, $isModule=0) {

        $adminMenus = $this->getAdminMenus($isModule);

        $this->deleteNotExistMenuCode($isModule, $adminMenus);

        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $query =
            ManageAuth::select('manage_auth.*', 'users.email as user_email', 'users.nick as user_nick', 'users.created_at as user_created_at')
            ->where('isModule', $isModule)
            ->leftJoin('users', 'manage_auth.user_id', '=', 'users.id');

        // 검색
        if($keyword) {
            $query = $query->where('users.email', 'like', $keyword);
        }

        // 정렬
        if($order) {
            $query = $query->orderBy('user_'. $order, $direction);
        } else {
            $query = $query->orderBy('user_id')->orderBy('menu');
        }

        $manageAuthList = $query->paginate(Cache::get('config.homepage')->pageRows);
        $manageAuthList = $this->cookingMenuName($isModule, $adminMenus, $manageAuthList);

        return [
            'menus' => $adminMenus,
            'manageAuthList' => $manageAuthList,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
        ];
    }

    public function storeManageAuth($request, $isModule) {
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return '존재하는 회원이 아닙니다.';
        }

        $authData = [];
        if($request->r) {
            $authData[] = $request->r;
        }
        if($request->w) {
            $authData[] = $request->w;
        }
        if($request->d) {
            $authData[] = $request->d;
        }

        $existManageAuth = ManageAuth::where([
            'user_id' => $user->id,
            'menu' => $request->menu
        ])->first();
        if($existManageAuth) {  // 관리 권한 변경
            $result = ManageAuth::where([
                'user_id' => $user->id,
                'menu' => $request->menu,
            ])->update([
                'auth' => implode(',', $authData)
            ]);
            if($result) {
                $this->forgetAdminMenuCache($isModule, $user);

                return $user->email. '회원님의 권한을 변경하였습니다.';
            }
        } else {    // 관리 권한 추가
            $result = ManageAuth::insert([
                'user_id' => $user->id,
                'menu' => $request->menu,
                'auth' => implode(',', $authData),
                'isModule' => $isModule
            ]);
            if($result) {
                $this->forgetAdminMenuCache($isModule, $user);

                return $user->email. '회원님의 권한을 추가하였습니다.';
            }
        }

        return $user->email. '회원님의 권한 추가에 실패하였습니다.';
    }

    public function deleteManageAuth($ids, $isModule=0) {
        $idArr = explode(',', $ids);

        // 권한을 삭제하려는 유저의 관리자 메뉴 캐시 삭제
        $manageAuthUsers = ManageAuth::select('user_id')->whereIn('id', $idArr)->get();
        $userIds = [];
        foreach ($manageAuthUsers as $manageAuthUser) {
            $userIds[] = $manageAuthUser->user_id;
        }
        $userIds = array_values(array_unique($userIds));
        $users = User::whereIn('id', $userIds)->get();
        foreach($users as $user) {
            $this->forgetAdminMenuCache($isModule, $user);
        }

        // 부여한 권한 삭제
        if(ManageAuth::destroy($idArr)) {
            return '선택한 항목을 삭제하였습니다.';
        }
        return '선택한 항목 삭제에 실패하였습니다.';
    }

    // type에 따라 관리자 메뉴를 가져온다.
    private function getAdminMenus($isModule)
    {
        $adminMenus = [];
        if(!$isModule) {
            $adminMenus = config('menu');   // 전체 메뉴 리스트
        } else {
            $modules = Module::all();
            foreach($modules as $module) {
                $adminMenus[$module->getLowerName()] = [
                    $module->getName(), $module->getAdminLink(), 0
                ];
            }
        }

        return $adminMenus;
    }

    // 메뉴 코드가 변경되어서 존재하지 않는 메뉴이면 해당 row 삭제.
    private function deleteNotExistMenuCode($isModule, $adminMenus)
    {
        $authList = ManageAuth::where('isModule', $isModule)->get();
        foreach($authList as $auth) {
            if( !array_key_exists($auth->menu, $adminMenus) ) {
                ManageAuth::destroy($auth->id);
            } else if ( !User::find($auth->user_id) ) { // 존재하지 않는 회원에게 부여된 권한인 경우 해당 row 삭제.
                ManageAuth::destroy($auth->id);
            }
        }
    }

    // 해당 사용자의 메뉴 캐시를 삭제
    private function forgetAdminMenuCache($isModule, $user)
    {
        if(!$isModule) {
            Cache::forget($user->id_hashkey.'_admin_primary_menu');
            Cache::forget($user->id_hashkey.'_admin_sub_menu');
        }
    }

    // 메뉴명에 설명 덧붙임
    private function cookingMenuName($isModule, $adminMenus, $manageAuthList)
    {
        if(!$isModule) {
            foreach($manageAuthList as $manageAuth) {
                $manageAuth->menu .= ' '. $adminMenus[$manageAuth->menu][0];
            }
        }

        return $manageAuthList;
    }
}
