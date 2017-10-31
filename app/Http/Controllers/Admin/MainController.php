<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BoardNew;
use App\Models\Point;
use Cache;
use DB;

class MainController extends Controller
{
    public $boardModel;

    public function __construct()
    {
        $this->middleware('admin');
        $this->boardModel = app()->tagged('board')[0];
    }

    public function index()
    {
        $pageRows = 5;
        $userList = $this->getUserList($pageRows);
        $newList = $this->getNewList($pageRows);
        $pointList = $this->getPointList($pageRows);

        $params = array_collapse([
            $userList, $newList, $pointList
        ]);
        $params = array_add($params, 'pageRows', $pageRows);
        $theme = cache('config.theme')->name;

        return view("admin.admin", $params);
    }

    private function getUserList($pageRows)
    {
        $interceptUsers = 0;
        $leaveUsers = 0;

        $query =
            User::select('users.*',
                DB::raw('
                ( select count(group_user.id)
                    from '. env('DB_PREFIX'). 'group_user as group_user
                    where group_user.user_id = '. env('DB_PREFIX'). 'users.id
                ) as count_groups'
                )
            );

        // 최고 관리자가 아니면 관리자보다 등급이 같거나 낮은 사람만 조회가능.
        if( !auth()->user()->isSuperAdmin() ) {
            $query = $query->where('level', '<=', auth()->user()->level);
        }

        $users = $query->latest()->paginate($pageRows);
        if(isDemo()) {
            foreach($users as $user) {
                $user->nick = invisible($user->nick);
                $user->email = invisible($user->email);
            }
        }
        $interceptUsers = $query->whereNotNull('intercept_date')->count();
        $leaveUsers = $query->whereNotNull('leave_date')->count();

        return [
            'users' => $users,
            'interceptUsers' => $interceptUsers,
            'leaveUsers' => $leaveUsers,
        ];
    }

    private function getNewList($pageRows)
    {
        $boardNewList =
            BoardNew::select('board_news.*', 'boards.table_name', 'boards.subject', 'groups.subject as group_subject', 'groups.id as group_id')
            ->leftJoin('boards', 'boards.id', '=', 'board_news.board_id')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->where('boards.use_search', 1)
            ->orderBy('board_news.id', 'desc')
            ->paginate($pageRows);

        $boardNew = new BoardNew();
        $boardNewList = $boardNew->processBoardNewList($boardNewList);
        if(isDemo()) {
            foreach($boardNewList as $new) {
                $new->name = invisible($new->name);
            }
        }


        return [ 'boardNews' => $boardNewList ];
    }

    private function getPointList($pageRows)
    {
        $points = Point::with('user')->orderBy('id', 'desc')->paginate($pageRows);

        foreach($points as $point) {
            if(!str_contains($point->rel_table, '@')) {
                $board = $this->boardModel::getBoard($point->rel_table, 'table_name');
                if($board) {
                    $point->rel_table = $board->id;
                }
            }
            if(isDemo()) {
                $point->user->email = invisible($point->user->email);
                $point->user->nick = invisible($point->user->nick);
            }
        }

        return [ 'points' => $points ];
    }
}
