<?php

namespace App\Http\Middleware;

use Closure;
use Doctrine\DBAL\Driver\PDOException;
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
        if(File::exists($path) && config('database.connections.mysql.database') && config('database.connections.mysql.username') && config('database.connections.mysql.password') ) {
            return view('install.error', ['type' => 'already']);
        } else {
            return $next($request);
        }
    }
}
