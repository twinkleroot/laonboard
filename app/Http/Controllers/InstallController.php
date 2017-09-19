<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\InstallRequest;
use File;
use Artisan;
use DB;
use Cache;
use Exception;
use Doctrine\DBAL\Driver\PDOException;
use Carbon\Carbon;
use App\User;
use App\Admin\Config;

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
            'agree' => $request->filled('agree') ? $request->agree : ''
        ];
        return view('install.form', $params);
    }

    public function setup(InstallRequest $request)
    {
        // 1. .env파일에 App 정보, DB 정보를 셋팅한다.
        $this->setEnv($request);
        // 2. DB 연결 확인
        try {
            DB::getPdo();
        } catch (PDOException $e) {
            return view('install.setup_result', ['dbError' => 1, 'message' => $e->getMessage()]);
        }
        // 3. App Key 생성
        Artisan::call('key:generate');
        // 4. 파일 쓰기를 위한 폴더 public/storage로 링크 걸기
        Artisan::call('storage:link');
        // 5. DB 구성
        Artisan::call('migrate:refresh');
        // 6. 입력받은 관리자 데이터로 관리자 회원 추가
        $this->addAdmin($request);
        // 7. 환경 설정 기본값 데이터 추가
        $this->addBasicConfig($request);
        // 8. 모든 설정 캐시를 초기화.
        Artisan::call('config:clear');

        return view('install.setup_result');
    }

    // .env 와 config/database 설정
    private function setEnv($request)
    {
        Artisan::call('env:set', ['key' => 'APP_ENV', 'value' => 'local']);
        Artisan::call('env:set', ['key' => 'APP_KEY', 'value' => '']);
        Artisan::call('env:set', ['key' => 'APP_DEBUG', 'value' => 'false']);
        Artisan::call('env:set', ['key' => 'APP_LOG', 'value' => 'daily']);
        Artisan::call('env:set', ['key' => 'APP_LOG_LEVEL', 'value' => 'debug']);
        Artisan::call('env:set', ['key' => 'APP_URL', 'value' => $request->appUrl]);
        Artisan::call('env:set', ['key' => 'DB_CONNECTION', 'value' => 'mysql']);
        Artisan::call('env:set', ['key' => 'DB_HOST', 'value' => $request->mysqlHost]);
        Artisan::call('env:set', ['key' => 'DB_PORT', 'value' => $request->mysqlPort]);
        Artisan::call('env:set', ['key' => 'DB_DATABASE', 'value' => $request->mysqlDb]);
        Artisan::call('env:set', ['key' => 'DB_USERNAME', 'value' => $request->mysqlUser]);
        Artisan::call('env:set', ['key' => 'DB_PASSWORD', 'value' => $request->mysqlPass]);
        Artisan::call('env:set', ['key' => 'DB_PREFIX', 'value' => $request->tablePrefix]);
        Artisan::call('env:set', ['key' => 'BROADCAST_DRIVER', 'value' => 'log']);
        Artisan::call('env:set', ['key' => 'CACHE_DRIVER', 'value' => 'file']);
        Artisan::call('env:set', ['key' => 'SESSION_DRIVER', 'value' => 'file']);
        Artisan::call('env:set', ['key' => 'QUEUE_DRIVER', 'value' => 'sync']);
        Artisan::call('env:set', ['key' => 'MAIL_DRIVER', 'value' => 'mail']);

        config(['app.env' => 'local']);
        config(['app.debug' => 'true']);
        config(['app.log' => 'daily']);
        config(['app.log_level' => 'debug']);
        config(['app.url' => $request->appUrl]);
        config(['database.connections.mysql.host' => $request->mysqlHost]);
        config(['database.connections.mysql.port' => $request->mysqlPort]);
        config(['database.connections.mysql.database' => $request->mysqlDb]);
        config(['database.connections.mysql.username' => $request->mysqlUser]);
        config(['database.connections.mysql.password' => $request->mysqlPass]);
        config(['database.connections.mysql.prefix' => $request->tablePrefix]);
    }

    // 관리자 정보 회원 등록
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

        $userId = User::insert($admin);
        $user = User::find($userId);
        $user->id_hashkey = str_replace("/", "-", bcrypt($userId));
        $user->save();

    }

    // 기본환경설정 기본값 셋팅
    private function addBasicConfig($request)
    {

        $configNames = [
            'homepage', 'board', 'join', 'cert', 'email.default', 'email.board', 'email.join', 'theme', 'skin', 'sns', 'extra'
        ];

        config(['gnu.superAdmin' => $request->adminEmail]);

        // 설정 캐시 등록
        foreach($configNames as $configName) {
            $this->registerConfigCache($configName);
        }
    }

    // 기본환경설정 캐시 등록
    private function registerConfigCache($configName)
    {
        Cache::forever("config.$configName", $this->getConfig($configName));
    }

    // 기본환경설정 get || ( create && get )
    private function getConfig($configName)
    {
        $configModel = new Config(); // 캐시에 저장할 때만 객체 생성
        $config = Config::where('name', 'config.'. $configName)->first();
        if(is_null($config)) {
            $config = $configModel->createConfigController($configName);
        }
        return $configModel->pullConfig($config);
    }

}
