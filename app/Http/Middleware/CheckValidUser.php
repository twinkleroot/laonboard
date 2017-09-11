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
            return alert('탈퇴하거나 가입하지 않은 회원입니다.');
        }

        return $next($request);
    }
}
