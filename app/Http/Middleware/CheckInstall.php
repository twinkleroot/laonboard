<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class CheckInstall
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
        if(File::exists($path)) {
            return $next($request);
        } else {
            return redirect('/install/index');
        }

    }
}
