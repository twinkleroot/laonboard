<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupUser;
use App\Group;
use App\User;

// 공통 기능
class CommonController extends Controller
{
    // 관리자 검색 기능
    public function search(Request $request)
    {
        $param = $request->all();
        $searchData = [];
        $view = '';

        switch ($param['admin_page']) {
            // 게시판 그룹 관리에서 검색할 때
            case 'boardGroup':
                $searchData = [
                    'groups' => Group::where($param['kind'], 'like', '%'.$param['keyword'].'%')->get()
                ];
                $view = 'admin.groups.index';
                break;
            // 그룹 접근 가능 회원에서 검색할 때
            case 'accessibleUsers':
                $group = Group::find($param['groupId']);
                $users = $group->users()
                        ->select(\DB::raw('users.*, count.count_groups'))
                        ->leftJoin(
                            \DB::raw('(select users.id as id, count(group_user.id) as count_groups
                            from group_user
                            left join users
                            on group_user.user_id = users.id
                            group by users.id) as count'),
                            'users.id', '=', 'count.id'
                        )
                        ->where('users.'.$param['kind'], 'like', '%'.$param['keyword'].'%')
                        ->get();
                $searchData = [
                    'group' => $group,
                    'users' => $users
                ];
                $view = 'admin.group_user.accessible_user_list';
                break;
            case '':
                // case 추가에 따라 사용하는 모델도 추가해야 한다.
                break;
            default:
                # code...
                break;
        }

        return view($view, $searchData);
    }
}
