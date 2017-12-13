<?php

namespace App\Http\Middleware;

use Closure;

class CheckAccessFolder
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
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            $paths = [storage_path(), base_path('bootstrap/cache')];
            $results = [];
            foreach ($paths as $path) {
                if (!(is_readable($path) && is_writeable($path) && is_executable($path))) {
                    $results[$path] = 1;
                }
            }
            if(notNullCount($results) > 0) {
                return view('install.error', [ 'results' => $results ]);
            }
        }

        return $next($request);
    }
}
