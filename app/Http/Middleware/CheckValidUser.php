<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

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
        $user = User::find(auth()->user()->id);
        if(is_null($user)) {
            return redirect(route('message'))
               ->with('message', '탈퇴하거나 가입하지 않은 회원입니다.');
        }

        return $next($request);
    }
}
