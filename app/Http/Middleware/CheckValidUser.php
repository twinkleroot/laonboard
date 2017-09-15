<?php

namespace App\Http\Middleware;

use Closure;

class CheckValidUser
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
        if(!auth()->check()) {
            return alert('회원정보가 존재하지 않습니다.\\n\\n탈퇴한 회원일 수 있습니다.');
        }

        if($request->toUser) {
            $user = getUser($request->toUser);
            if(!$user->id || $user->leave_date) {
                return alert('회원정보가 존재하지 않습니다.\\n\\n탈퇴한 회원일 수 있습니다.');
            }

            if( !$user->open && !$user->isSuperAdmin() && auth()->user()->id != $user->id) {
                return alert('해당 회원이 정보공개를 하지 않았습니다.');
            }
        }


        return $next($request);
    }
}
