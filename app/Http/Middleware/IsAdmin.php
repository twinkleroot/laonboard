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
        // 현재 메뉴가 어디인지 알아내기? url()->current()?? 아니면 name?? route값?
        // $manageAuth = ManageAuth::where('user_id', $user->id)->get();
        // foreach($manageAuth->toArray() as $manage) {
        //     $manage['menu']
        // }
        // if($user->id )
        return redirect(route('message'))
           ->with('message',  '최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.')
           ->with('redirect', '/');
    }
}
