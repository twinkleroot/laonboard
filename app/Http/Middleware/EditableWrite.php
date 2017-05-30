<?php

namespace App\Http\Middleware;

use Closure;
use App\Config;
use App\Board;
use App\Group;
use App\User;
use App\Write;

class EditableWrite
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
        $currentUser = is_null($user) ? '' : $user->email;
        $superAdmin = Config::getConfig('config.homepage')->superAdmin;
        $board = Board::find($request->boardId);
        $boardAdmin = $board->admin;
        $groupAdmin = Group::find($board->group_id)->admin;
        $writeModel = new Write($request->boardId);
        $writeModel->setTableName($board->table_name);
        $write = $writeModel->find($request->writeId);
        $writeUser = $write->user_id == 0 ? '' : User::find($write->user_id);

        $message = '';
        $word = '수정';
        if(strpos($request->getRequestUri(), 'delete')) {
            $word = '삭제';
        }
        if ($currentUser == $superAdmin) {// 최고관리자 통과
            ;
        } else if ($currentUser == $groupAdmin) { // 그룹관리자
            if ($user->level < $writeUser->level)  { // 자신의 레벨이 크거나 같다면 통과
                $message = '자신의 권한보다 높은 권한의 회원이 작성한 글은 '. $word. '할 수 없습니다.';
            }
        } else if ($currentUser == $boardAdmin) { // 게시판관리자이면
            dd($currentUser, $boardAdmin);
            if ($user->level < $writeUser->level) { // 자신의 레벨이 크거나 같다면 통과
                $message = '자신의 권한보다 높은 권한의 회원이 작성한 글은 '. $word. '할 수 없습니다.';
            }
        } else if ($user) {
            if ($currentUser != $writeUser->email) {
                $message = '자신의 글이 아니므로 '. $word. '할 수 없습니다.';
            }
        } else {
            if ($writeUser == '') {
                $message = '로그인 후 '. $word. '하세요.';
                return redirect('message')
                    ->with('message', $message)
                    ->with('redirect',
                        route('login'). '?url='. route('board.view', ['boardId' => $board->id, 'writeId' => $write->id]));
            }
        }

        if($message != '') {
            return redirect(route('message'))->with('message', $message);
        }

        return $next($request);
    }
}
