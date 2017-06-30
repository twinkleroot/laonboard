<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;

class ManageAuth extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $table = 'manage_auth';
    public $timestamps = false;

    public function getIndexParams($request) {
        $authList = ManageAuth::all();
        $adminMenus = config('menu');   // 전체 메뉴 리스트
        foreach($authList as $auth) {
            // 메뉴 코드가 변경되어서 존재하지 않는 메뉴이면 해당 row 삭제.
            if( !array_key_exists($auth->menu, $adminMenus) ) {
                ManageAuth::destroy($auth->id);
            } else if ( !User::find($auth->user_id) ) { // 존재하지 않는 회원에게 부여된 권한인 경우 해당 row 삭제.
                ManageAuth::destroy($auth->id);
            }
        }

        $keyword = isset($request->keyword) ? $request->keyword : '';
        $order = isset($request->order) ? $request->order : '';
        $direction = isset($request->direction) ? $request->direction : '';
        $query = ManageAuth::selectRaw('manage_auth.*, users.email as user_email, users.nick as user_nick')
            ->leftJoin('users', 'manage_auth.user_id', '=', 'users.id');

        if($keyword) {
            $query = $query->where('users.email', 'like', $keyword);
        }

        if($order) {
            $query = $query->orderBy('users.'. $order, $direction);
        } else {
            $query = $query->orderByRaw('manage_auth.user_id, manage_auth.menu');
        }

        $manageAuthList = $query->paginate(Cache::get('config.homepage')->pageRows);

        foreach($manageAuthList as $manageAuth) {
            $manageAuth->menu .= ' '. $adminMenus[$manageAuth->menu][0];
        }

        return [
            'menus' => $adminMenus,
            'manageAuthList' => $manageAuthList,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
        ];
    }

    public function storeManageAuth($request) {
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return '존재하는 회원이 아닙니다.';
        }

        // eloquent에서 set type의 데이터를 어떻게 넣을 수 있는지 찾게 되면 변경하자
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
        if($existManageAuth) {
            $result = ManageAuth::where([
                'user_id' => $user->id,
                'menu' => $request->menu,
            ])->update([
                'auth' => implode(',', $authData)
            ]);
            if($result) {
                return $user->email. '회원님의 권한을 변경하였습니다.';
            }
        } else {
            $result = ManageAuth::create([
                'user_id' => $user->id,
                'menu' => $request->menu,
                'auth' => implode(',', $authData)
            ]);
            if($result) {
                return $user->email. '회원님의 권한을 추가하였습니다.';
            }
        }

        return $user->email. '회원님의 권한 추가에 실패하였습니다.';
    }

    public function deleteManageAuth($ids) {
        $idArr = explode(',', $ids);

        if(ManageAuth::destroy($idArr)) {
            return '선택한 항목을 삭제하였습니다.';
        }
        return '선택한 항목 삭제에 실패하였습니다.';
    }
}
