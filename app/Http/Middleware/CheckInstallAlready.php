<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;

class CheckInstallAlready
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
        $file = '.env';
        $path = base_path($file);
        if(!File::exists($path)) {
            return $next($request);
        } else {
            return alert("프로그램이 이미 설치되어 있습니다.\\n새로 설치하시려면 $path 파일을 삭제 하신 후 새로고침 하십시오.");
        }
    }
}
