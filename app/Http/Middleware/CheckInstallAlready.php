<?php

namespace App\Http\Middleware;

use Closure;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if(File::exists($path) && env('DB_DATABASE') && env('DB_USERNAME') && env('DB_PASSWORD') && env('DB_PREFIX')) {
            try {
                DB::getPdo();
            } catch (PDOException $e) {
                return $next($request);
            }

            if(!Schema::hasTable('configs')) {
                return $next($request);
            }

            return view('install.error', ['type' => 'already']);
        } else {
            return $next($request);
        }
    }
}
