<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\ReCaptcha;
use App\User;
use Carbon\Carbon;
use App\Config;
use App\Point;

class UserController extends Controller
{

    public $config;
    public $rulePassword;

    public function __construct(Config $config)
    {
        $this->config = Config::getConfig('config.join');
        $this->rulePassword = Config::getRulePassword('config.join');
    }

    // 회원 정보 수정 폼
    public function edit()
    {
        $user = Auth::user();

        // 정보공개 변경여부
        $openChangable = $this->openChangable($user, Carbon::now());

        return view('user.edit')
            ->with('user', $user)
            ->with('config', $this->config)
            ->with('nickChangable', $this->nickChangable($user, Carbon::now())) // 닉네임 변경여부
            ->with('openChangable', $openChangable[0])                          // 정보공개 변경 여부
            ->with('dueDate', $openChangable[1])                                // 정보공개 언제까지 변경 못하는지 날짜
            ->with('recommend', User::recommendedPerson($user))                // 추천인 닉네임 id로 가져오기
            ;
    }

    // 닉네임 변경여부
    public function nickChangable($user, $current)
    {
        // 현재 시간과 로그인한 유저의 닉네임변경시간과의 차이
        $nickDiff = $current->diffInDays($user->nick_date);
        // 닉네임 변경 여부
        $nickChangable = false;
        if($nickDiff > $this->config->nickDate) {
            $nickChangable = true;
        }

        return $nickChangable;
    }

    // 정보공개 변경 여부
    public function openChangable($user, $current)
    {
        $openChangable = array(false, $current);

        $openDate = $user->open_date;

        if(is_null($openDate)) {
            $openChangable[0] = true;
        } else {
            $openDiff = $current->diffInDays($openDate);
            if($openDiff >= $this->config->openDate) {
                $openChangable[0] = true;
            }
            $openChangable[1] = $openDate->addDays($this->config->openDate);
        }

        return $openChangable;
    }

    // 회원 정보 수정 폼에 앞서 비밀번호 한번 더 확인하는 폼
    public function checkPassword()
    {
        $user = Auth::user();
        if(is_null($user->password)) {
            return view('user.set_password');   // 최초 비밀번호 설정
        } else {
            return view('user.confirm_password')->with('email', $user->email);
        }
    }

    // 비밀번호 비교
    public function confirmPassword(Request $request)
    {
        $user = Auth::user();
        $email = $user->email;

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::attempt(['email' => $email, 'password' => $request->get('password') ], false, false)) {
            return redirect(route('user.edit'));
        } else {
            return redirect(route('user.checkPassword'))->with('message', '비밀번호가 틀립니다.');
        }
    }

    // 최초 비밀번호 설정
    public function setPassword(Request $request)
    {
        $rule = $this->setPasswordRule();
        $this->validate($request, $rule);

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return redirect(route('user.edit'));
    }

    // 회원 정보 수정
    public function update(Request $request)
    {
        $user = Auth::user();
        $openChangable = $this->openChangable($user, Carbon::now());

        // 구글 리캡챠 체크
        if(!ReCaptcha::reCaptcha($request)) {
            return view('user.edit')->withErrors(['reCapcha' => '자동등록방지 입력이 틀렸습니다. 다시 입력해 주십시오.'])
            ->with('user', $user)
            ->with('config', $this->config)
            ->with('nickChangable', $this->nickChangable($user, Carbon::now())) // 닉네임 변경여부
            ->with('openChangable', $openChangable[0])                          // 정보공개 변경 여부
            ->with('dueDate', $openChangable[1])                                // 정보공개 언제까지 변경 못하는지 날짜
            ->with('recommend', User::recommendedPerson($user))                // 추천인 닉네임 id로 가져오기
            ;
        }

        $rule = $this->setRulePassword();
        $this->validate($request, $rule);

        if($request->get('password') !== '') {
            $user->password = bcrypt($request->get('password'));
            $user->save();
        }

        // 현재 시간 date type으로 받기
        $nowDate = Carbon::now()->toDateString();

        // 추천인 닉네임 받은 것을 해당 닉네임의 id로 조회
        $recommendedId = '';
        if($request->has('recommend')) {
            $recommendedUser = User::where([
                'nick' => $request->get('recommend'),
            ])->first();

            if(is_null($recommendedUser)) {
                return redirect(route('user.edit'))
                        ->withErrors(['recommend' => '추천인이 존재하지 않습니다.']);
            }
            $recommendedId = $recommendedUser->id;

            // 추천인에게 포인트 주기.
            $rel_table = '@users';
            $rel_email = $recommendedUser->email;
            $rel_action = $user->email . ' 추천';

            // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
            $existPoint = Point::checkPoint($rel_table, $rel_email, $rel_action);
            if(is_null($existPoint)) {
                $content = $user->email . '의 추천인';
                $pointToGive = Point::pointType('recommend');
                Point::givePoint($pointToGive, $rel_table, $rel_email, $rel_action, $content);
                $recommendedUser->point = $recommendedUser->point + $pointToGive;
            }

            $recommendedUser->save();
        }

        $user->update([
            'id_hashkey' => bcrypt($user->id),  // 회원정보수정때마다 id_hashkey를 변경한다.
            'name' => $request->get('name'),
            'nick' => $request->has('nick') ? $request->get('nick') : $user->nick,
            'nick_date' => $request->has('nick') ? $nowDate : $user->nick_date,
            'homepage' => $request->get('homepage'),
            'hp' => $request->get('hp'),
            'tel' => $request->get('tel'),
            'addr1' => $request->get('addr1'),
            'addr2' => $request->get('addr2'),
            'addr3' => $request->get('addr3'),
            'zip' => $request->get('zip'),
            'signature' => $request->get('signature'),
            'profile' => $request->get('profile'),
            'memo' => $request->get('memo'),
            'mailing' => $request->has('mailing') ? $request->get('mailing') : 0,
            'sms' => $request->has('sms') ? $request->get('sms') : 0,
            'recommend' => $request->has('recommend') ? $recommendedId : $user->recommend,
        ]);

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->has('open') || $user->open != $request->get('open')) {
            $user->open = $request->get('open');
            $user->open_date = $nowDate;
            $user->save();
        }

        return redirect('/index')->with('message', $user->nick . '님의 회원정보가 변경되었습니다.');
    }

    public function setRulePassword()
    {
        $rule = User::$rulesPassword;
        $rule = array_add($rule, 'password', $this->rulePassword);

        return $rule;
    }

}
