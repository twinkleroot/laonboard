<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle($request, Closure $next, $level)
     {
         $user = Auth::user();
         if($user->level < $level) {
             return redirect(route('message'))->with('message', '권한이 없어서 접근할 수 없습니다.');
         }
         return $next($request);
     }
}
