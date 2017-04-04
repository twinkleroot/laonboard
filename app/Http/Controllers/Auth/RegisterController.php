<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Config;
use App\User;
use App\ReCaptcha;

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

    // 라라벨에서 기본으로 제공해 주는 회원가입 트레이트 사용 안함.
    // use RegistersUsers;

    public $request;
    public $config;
    public $rulePassword;
    public $userModel;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, User $userModel)
    {
        $this->middleware('guest');

        $this->request = $request;
        $this->config = Config::getConfig('config.join');
        $this->rulePassword = Config::getRulePassword('config.join', $this->config);
        $this->userModel = $userModel;
    }

    // 회원 가입 페이지
    public function join()
    {
        return view('auth.register');
    }

    // 회원 가입 수행
    public function register(Request $request)
    {
        if(ReCaptcha::reCaptcha($request)) {    // 구글 리캡챠 체크
            $rule = array_add($this->userModel->rulesRegister, 'password', $this->rulePassword);
            $this->validate($request, $rule);

            $user = $this->userModel->userJoin($request, $this->config);
            Auth::login($user);

            return redirect(route('user.welcome'));
        } else {
            return redirect(route('user.join'))->withErrors(['reCaptcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        }
    }

}
