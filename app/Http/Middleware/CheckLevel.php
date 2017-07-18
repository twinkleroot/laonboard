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
            return alertRedirect($message);
         }
         return $next($request);
     }
}
