<?php

namespace App\Http\Controllers\Install;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Artisan;
use DB;
use Carbon\Carbon;
use App\User;

class InstallController extends Controller
{
    public function index()
    {
        return view('install.index');
    }

    public function license()
    {
        return view('install.license');
    }

    public function form(Request $request)
    {
        $params = [
            'agree' => $request->has('agree') ? $request->agree : ''
        ];
        return view('install.form', $params);
    }

    public function setup(Request $request)
    {
        // 1. .env.example 파일로 .env 파일을 생성한다.
        // $path = base_path('.env.example');
        // File::copy($path, base_path('.env'));
        $this->setEnv($request);
        // 4. 모든 설정파일을 하나로 캐시한다.
        Artisan::call('config:cache');
        // DB 연결 확인
        DB::getPdo();
        // 2. key 생성
        Artisan::call('key:generate');
        // 3. DB 구성
        Artisan::call('migrate');
        // 5. 입력받은 관리자 데이터로 관리자 회원 추가
        $this->addAdmin($request);
        // 6. public 폴더에 접근할 수 있도록 심볼릭 링크 추가
        File::link(base_path('/public'), base_path('../'). 'public');
        // 7. 파일 업로드를 위해 public폴더 아래로 storage 심볼릭 링크 추가
        Artisan::call('storage:link');
        // File::link(base_path('/storage/app/public'), public_path('storage'));

        return view('install.setup_result');
    }

    private function setEnv($request)
    {
        Artisan::call('env:set', [
            'key' => 'APP_URL', 'value' => $request->appUrl
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_HOST', 'value' => $request->mysqlHost
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_PORT', 'value' => $request->mysqlPort
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_DATABASE', 'value' => $request->mysqlDb
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_USERNAME', 'value' => $request->mysqlUser
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_PASSWORD', 'value' => $request->mysqlPass
        ]);
        Artisan::call('env:set', [
            'key' => 'DB_PREFIX', 'value' => $request->tablePrefix
        ]);
    }

    private function addAdmin($request)
    {
        $nowDate = Carbon::now()->toDateString();

        $admin = [
            'name' => $request->adminNick,
            'nick' => $request->adminNick,
            'nick_date' => $nowDate,
            'email' => $request->adminEmail,
            'password' => bcrypt($request->adminPass),
            'level' => 10,
            'point' => 9999999,
            'mailing' => 1,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'email_certify' => Carbon::now(),
        ];

        User::insert($admin);
        $user = User::find(DB::getPdo()->lastInsertId());
        $user->id_hashkey = str_replace("/", "-", bcrypt($user->id));
        $user->save();
    }
}
