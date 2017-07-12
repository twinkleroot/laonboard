<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\BoardNew;
use App\Board;
use App\Point;
use Cache;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
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

        return view('admin.index', $params);
    }

    private function getUserList($pageRows)
    {
        $interceptUsers = 0;
        $leaveUsers = 0;

        $query =
            User::selectRaw('users.*,
                ( select count(group_user.id)
                    from group_user
                    where group_user.user_id = users.id
                ) as count_groups'
            );

        // 최고 관리자가 아니면 관리자보다 등급이 같거나 낮은 사람만 조회가능.
        if( !auth()->user()->isSuperAdmin() ) {
            $query = $query->where('level', '<=', auth()->user()->level);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($pageRows);
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
            BoardNew::selectRaw('board_news.*, boards.table_name, boards.subject, boards.mobile_subject, groups.subject as group_subject, groups.id as group_id')
            ->leftJoin('boards', 'boards.id', '=', 'board_news.board_id')
            ->leftJoin('groups', 'groups.id', '=', 'boards.group_id')
            ->where('boards.use_search', 1)
            ->orderBy('board_news.id', 'desc')
            ->paginate($pageRows);

        $boardNew = new BoardNew();
        $boardNewList = $boardNew->processBoardNewList($boardNewList);

        return [ 'boardNews' => $boardNewList ];
    }

    private function getPointList($pageRows)
    {
        $points = Point::orderBy('id', 'desc')->paginate($pageRows);

        foreach($points as $point) {
            $board = Board::where('table_name', $point->rel_table)->first();
            if($board) {
                $point->rel_table = $board->id;
            }
        }

        return [ 'points' => $points ];
    }
}
