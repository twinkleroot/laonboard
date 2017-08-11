<?php

namespace App\Http\Controllers\Install;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use Artisan;
use DB;
use Exception;
use Doctrine\DBAL\Driver\PDOException;
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
        // 3. 파일 쓰기를 위한 폴더 public/storage로 링크 걸기
        Artisan::call('storage:link');
        // 4. DB 구성
        Artisan::call('migrate');
        // 5. 입력받은 관리자 데이터로 관리자 회원 추가
        $this->addAdmin($request);
        // 6. 환경 설정 기본값 데이터 추가
        $this->addBasicConfig($request);
        // 7. 모든 설정파일을 하나로 캐시한다.
        Artisan::call('config:cache');

        return view('install.setup_result');
    }

    // .env 와 config/database 설정
    private function setEnv($request)
    {
        Artisan::call('env:set', ['key' => 'APP_URL', 'value' => $request->appUrl]);
        Artisan::call('env:set', ['key' => 'DB_HOST', 'value' => $request->mysqlHost]);
        Artisan::call('env:set', ['key' => 'DB_PORT', 'value' => $request->mysqlPort]);
        Artisan::call('env:set', ['key' => 'DB_DATABASE', 'value' => $request->mysqlDb]);
        Artisan::call('env:set', ['key' => 'DB_USERNAME', 'value' => $request->mysqlUser]);
        Artisan::call('env:set', ['key' => 'DB_PASSWORD', 'value' => $request->mysqlPass]);
        Artisan::call('env:set', ['key' => 'DB_PREFIX', 'value' => $request->tablePrefix]);

        config(['database.connections.mysql.host' => $request->mysqlHost]);
        config(['database.connections.mysql.port' => $request->mysqlPort]);
        config(['database.connections.mysql.database' => $request->mysqlDb]);
        config(['database.connections.mysql.username' => $request->mysqlUser]);
        config(['database.connections.mysql.password' => $request->mysqlPass]);
        config(['database.connections.mysql.prefix' => $request->tablePrefix]);
    }

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

    // 설정 캐시 등록
    private function registerConfigCache($configName)
    {
        if(!Cache::has("config.$configName")) {
            Cache::forever("config.$configName", $this->getConfig($configName));
        }
    }

    // 설정 get || ( create && get )
    private function getConfig($configName)
    {
        $configModel = new Config(); // 캐시에 저장할 때만 객체 생성
        $config = Config::where('name', 'config.'. $configName)->first();
        if(is_null($config)) {
            $config = $configModel->createConfigController($configName);
        }
        return $configModel->pullConfig($config);
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
