<?php

namespace App\Http\Middleware;

use Closure;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        try {
            if(File::exists($path) && config('database.connections.mysql.database') && config('database.connections.mysql.username') && config('database.connections.mysql.password') ) {
                // DB 연결 확인
                DB::connection()->getPdo();
                return $next($request);
            } else {
                return redirect(route('install.index'));
            }
        } catch (PDOException $e) {
            dd('Database Connect Error!!!');
        }
    }
}
