<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use App\Config;
use App\Point;
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

    use RegistersUsers;

    public $request;
    public $config;
    public $rulePassword;
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
        $this->rulePassword = Config::getRulePassword('config.join');
    }

    // 구글 리캡챠 체크
    public function checkRecaptcha(Request $request)
    {
        // if(ReCaptcha::reCaptcha($request)) {
            $user = $this->create(array($request));
            Auth::login($user);

            return redirect(route('user.welcome'));
        // } else {
        //     return view('auth.register')->withErrors(['reCapcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        // }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return $user;
     */
    protected function create(array $data)
    {
        $rule = User::$rulesRegister;
        $rule = array_add($rule, 'password', $this->rulePassword);
        $this->validate($data[0], $rule);

        $nowDate = Carbon::now()->toDateString();

        // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
        // (회원 탈퇴 후 재가입하면서 포인트를 늘려가는 행위 차단을 위해)
        $rel_table = '@users';
        $rel_email = $data[0]['email'];
        $rel_action = '회원가입';
        $existPoint = Point::checkPoint($rel_table, $rel_email, $rel_action);
        if(is_null($existPoint)) {
            $content = '회원가입 축하';
            $pointToGive = Point::pointType('join');
            Point::givePoint($pointToGive, $rel_table, $rel_email, $rel_action, $content);
        }

        $userInfo = [
            'email' => $data[0]['email'],
            'password' => bcrypt($data[0]['password']),
            'nick' => $data[0]['nick'],
            'nick_date' => $nowDate,
            'mailing' => 1,
            'sms' => 1,
            'open' => 1,
            'open_date' => $nowDate,
            'today_login' => Carbon::now(),
            'login_ip' => $this->request->ip(),
            'ip' => $this->request->ip(),
            'point' => Point::pointType('join'),
        ];

        // 이메일 인증을 사용할 경우
        if($this->config->emailCertify == '1') {
            $addUserInfo = [
                'email_certify' => null,
                // 라우트 경로 구분을 위해 /는 제거해 줌.
                'email_certify2' => str_replace("/", "", bcrypt($this->request->ip() . Carbon::now()) ),
                'level' => 1,   // 인증하기 전 회원 레벨은 1
            ];
            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        } else {    // 이메일 인증을 사용하지 않을 경우
            $addUserInfo = [
                'email_certify' => Carbon::now(),
                'level' => $this->config->joinLevel,
            ];

            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        }
        // 입력받은 정보와 가공한 정보를 바탕으로 회원정보를 DB에 추가한다.
        $user = User::create($userInfo);

        // Users 테이블의 주 키인 id의 해시 값을 만들어서 저장한다. (게시글에 사용자 번호 노출 방지)
        $user->id_hashkey = str_replace("/", "", bcrypt($user->id));

        $user->save();

        return $user;
    }

}
