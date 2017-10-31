<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\GroupUser;

class CheckValidBoard
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
        $boardModel = app()->tagged('board')[0];
        $board = $boardModel::getBoard($request->boardName, 'table_name');
        if(!$board) {
            return alertRedirect('존재하지 않는 게시판입니다. 경로를 확인해 주세요.');
        }

        $group = $board->group;
        if($group->use_access) {
            if(! auth()->check()) {
                $msg = "비회원은 이 게시판에 접근할 권한이 없습니다.\\n\\n회원이시라면 로그인 후 이용해 보십시오.";
                return alertRedirect($msg, '/login?nextUrl='. $request->getRequestUri());
            }

            $user = auth()->user();
            if(!$user->isSuperAdmin() && !$user->isGroupAdmin($group)) {
                $count = GroupUser::where([
                    'group_id' => $group->id,
                    'user_id' => $user->id
                ])->count();
                if(!$count) {
                    return alert("접근 권한이 없으므로 글읽기가 불가합니다.\\n\\n궁금하신 사항은 관리자에게 문의 바랍니다.");
                }
            }
        }

        return $next($request);
    }
}
