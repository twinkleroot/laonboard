<?php

namespace App\Http\Middleware;

use Closure;
use App\ManageAuth;

class IsAdmin
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
        // 최고 관리자나 일반 관리자면
        if($user->isAdmin()) {
            return $next($request);
        }
        
        return redirect(route('message'))
           ->with('message',  '최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.')
           ->with('redirect', '/');
    }
}
