<?php

namespace App\Http\Middleware;

use Closure;

class IsSuperAdmin
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
        if(!auth()->user()->IsSuperAdmin()) {
            return alert('최고관리자만 접근 가능합니다.');
        }

        return $next($request);
    }
}
