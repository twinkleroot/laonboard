<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GroupUser;
use App\Group;
use App\Board;
use App\Point;
use App\User;
use Cache;
use DB;

// 공통 기능
class SearchController extends Controller
{

    public $pointModel;

    public function __construct(Point $point)
    {
        $this->middleware('level:10');

        $this->pointModel = $point;
    }

    // 관리자 검색 기능
    public function search(Request $request)
    {
        $param = $request->all();
        $searchData = [];
        $view = '';
        $config = Cache::get("config.homepage");

        switch ($param['admin_page']) {
            // 게시판 그룹 관리에서 검색할 때
            case 'boardGroup':
                $groups = DB::table('groups as g')
                    ->select(DB::raw('
                                g.id,
                                g.group_id,
                                g.subject,
                                g.admin,
                                g.use_access,
                                g.order,
                                g.device,
                                g.created_at,
                                (   select count(gu.id)
                                    from group_user as gu
                                    where gu.group_id = g.id
                                ) as count_users,
                                (   select count(b.id)
                                    from boards as b
                                    where b.group_id = g.id
                                ) as count_board'
                    ))
                    ->orderBy('g.created_at', 'desc')
                    ->where($param['kind'], 'like', '%'.$param['keyword'].'%')
                    ->paginate($config->pageRows);
                $searchData = ['groups' => $groups];
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
                        ->paginate($config->pageRows);
                $searchData = [
                    'group' => $group,
                    'users' => $users,
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
                                    ->paginate($config->pageRows);
                } else {
                    $boards = Board::where($param['kind'], 'like', '%'.$param['keyword'].'%')
                            ->paginate($config->pageRows);
                }
                $searchData = [
                    'boards' => $boards,
                    'groups' => Group::get(),
                ];
                $view = 'admin.boards.index';
                break;

            // 포인트 관리에서 검색할 때
            case 'point':
                $points;
                $sum = 0;
                $searchEmail = '';
                if($param['kind'] == 'content') {
                    $points = Point::where($param['kind'], 'like', '%'.$param['keyword'].'%')->orderBy('id', 'desc')
                            ->paginate($config->pageRows);
                    $sum = $this->pointModel->sumPoint();
                } else {
                    // 회원 이메일이나 닉네임으로 유저를 검색
                    $user = User::where( [$param['kind'] => $param['keyword']] )->first();
                    if(!is_null($user)) {
                        // 검색한 유저의 id로 포인트 테이블 조회
                        $points = Point::where(['user_id' => $user->id])->orderBy('id', 'desc')
                                ->paginate($config->pageRows);
                        $sum = $points->max('user_point');
                        $searchEmail = $user->email;
                    } else {
                        // 유저가 없으므로 user_id를 0으로 해서 조회한다. (null error 방지용)
                        $points = Point::where(['user_id' => 0])->orderBy('id', 'desc')
                                ->paginate($config->pageRows);
                        $sum = $this->pointModel->sumPoint();
                    }
                }

                $searchData = [
                    'points' => $points,
                    'sum' => $sum,
                    'searchEmail' => $searchEmail,
                ];
                $view = 'admin.points.index';
                break;
            case '':
                // case 추가에 따라 사용하는 모델도 추가해야 한다.
                break;
            default:
                # code...
                break;
        }

        // 공통으로 가져가는 데이터
        $commonData = [
            'kind' => $param['kind'],
            'keyword' => $param['keyword'],
            'config' => $config,
        ];

        // 검색결과 데이터 배열과 공통 데이터 배열을 검색결과 데이터 배열에 합친다.
        $searchData = array_collapse([$searchData, $commonData]);

        return view($view, $searchData);
    }
}
