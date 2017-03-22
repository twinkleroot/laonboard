<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\ReCaptcha;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Config;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    public $request;
    public $config;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
        $this->request = $request;
        $this->config = Config::getConfig('config.join');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, User::$rulesRegister);
    }

    // 구글 리캡챠 체크
    public function checkRecaptcha(Request $request)
    {
        if(ReCaptcha::reCaptcha($request)) {
            $this->create($request);
        } else {
            return view('auth.register')->withErrors(['reCapcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $nowDate = Carbon::now()->toDateString();

        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'nick' => $data['nick'],
            'nick_date' => $nowDate,
            'mailing' => 1,
            'sms' => 1,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'login_ip' => $this->request->ip(),
            'ip' => $this->request->ip(),
            'level' => $this->config->joinLevel,
            'point' => $this->config->joinPoint,
        ]);
    }

}
