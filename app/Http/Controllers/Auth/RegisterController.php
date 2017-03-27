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
use App\Point;
use Auth;

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
            // 이메일 인증을 사용하지 않을 경우 유저를 생성한 후 바로 로그인 시켜준다.
            if($this->config->emailCertify == '0') {
                Auth::login($user);
            }

            return view('user.completed_join')
                ->with('emailCertify', $this->config->emailCertify)
                ->with('user', $user)
            ;
        // } else {
        //     return view('auth.register')->withErrors(['reCapcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.']);
        // }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return redirect(route('home'));
     */
    protected function create(array $data)
    {
        $rule = User::$rulesRegister;
        $rule = array_add($rule, 'password', $this->rulePassword);
        $this->validate($data[0], $rule);

        $nowDate = Carbon::now()->toDateString();
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
            'level' => $this->config->joinLevel,
        ];

        // 이메일 인증을 사용할 경우
        if($this->config->emailCertify == '1') {
            $addUserInfo = [
                'email_certify' => null,
                'email_certify2' => bcrypt($this->request->ip() . Carbon::now()),
            ];
            $userInfo = array_collapse([$userInfo, $addUserInfo]);
        } else {    // 이메일 인증을 사용하지 않을 경우
            $rel_table = '@users';
            $rel_email = $data[0]['email'];
            $rel_action = '회원가입';

            // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
            // (회원 탈퇴 후 재가입하면서 포인트를 늘려가는 행위 차단을 위해)
            $existPoint = Point::checkPoint($rel_table, $rel_email, $rel_action);
            if(is_null($existPoint)) {
                $content = '회원가입 축하';
                $pointToGive = Point::pointType('join');
                Point::givePoint($pointToGive, $rel_table, $rel_email, $rel_action, $content);
                $userInfo = array_add($userInfo, 'point', $pointToGive);
                $user->point = $pointToGive;
            } else {
                $userInfo = array_add($userInfo, 'point', $existPoint->user_point);
            }
        }
        // 입력받은 정보와 가공한 정보를 바탕으로 회원정보를 DB에 추가한다.
        $user = User::create($userInfo);

        // Users 테이블의 주 키인 id의 해시 값을 만들어서 저장한다. (게시글에 사용자 번호 노출 방지)
        $user->id_hashkey = bcrypt($user->id);

        $user->save();

        return $user;
    }

}
