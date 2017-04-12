<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupUser;
use App\Group;
use App\Board;
use App\User;
use DB;

// 공통 기능
class CommonController extends Controller
{
    public function __construct()
    {
        $this->middleware('level:10');
    }

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
                    'groups' => Group::where($param['kind'], 'like', '%'.$param['keyword'].'%')->get(),
                    'kind' => $param['kind'],
                ];
                $view = 'admin.groups.index';
                break;
            // 그룹 접근 가능 회원에서 검색할 때
            case 'accessibleUsers':
                $group = Group::find($param['groupId']);
                $users = $group->users()
                        ->select(DB::raw('users.*, count.count_groups'))
                        ->leftJoin(
                            DB::raw('(select users.id as id, count(group_user.id) as count_groups
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
                    'users' => $users,
                    'keyword' => $param['keyword'],
                ];
                $view = 'admin.group_user.accessible_user_list';
                break;
            // 게시판 관리에서 검색할 때
            case 'board':
                $boards;
                if($param['kind'] == 'group_id') {
                    $boards = Board::select(DB::raw('boards.*, groups.subject'))
                                    ->leftJoin('groups', 'boards.group_id', '=', 'groups.id')
                                    ->where('groups.group_id', '=', $param['keyword'])
                                    ->get();
                } else {
                    $boards = Board::where($param['kind'], 'like', '%'.$param['keyword'].'%')->get();
                }
                $searchData = [
                    'boards' => $boards,
                    'groups' => Group::get(),
                    'kind' => $param['kind'],
                    'keyword' => $param['keyword'],
                ];
                $view = 'admin.boards.index';
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
