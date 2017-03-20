<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Carbon\Carbon;

class UserController extends Controller
{

    public function edit()
    {
        $user = Auth::user();
        $current = Carbon::now();

        // 현재 시간과 로그인한 유저의 닉네임변경시간과의 차이
        $nickDiff = $current->diffInDays($user->nick_date);
        // 닉네임 변경 여부
        $nickChangable = false;
        if($nickDiff > config('gnu.nickDate')) {
            $nickChangable = true;
        }

        // 정보공개 변경 여부
        $openChangable = false;
        $dueDate = $current;
        $openDate = $user->open_date;

        if(is_null($openDate)) {
            $openChangable = true;
        } else {
            $openDiff = $current->diffInDays($openDate);
            if($openDiff >= config('gnu.openDate')) {
                $openChangable = true;
            }
            $dueDate = $openDate->addDays(config('gnu.openDate'));
        }

        // 추천인 닉네임 구하기
        $recommendedNick = '';
        if(!is_null($user->recommend)) {
            $recommendedNick = User::where([
                'id' => $user->recommend,
            ])->first()->nick;
        }

        return view('user.edit')
            ->with('user', $user)
            ->with('nickChangable', $nickChangable)
            ->with('openChangable', $openChangable)
            ->with('dueDate', $dueDate)
            ->with('recommend', $recommendedNick);
            ;
    }

    public function checkPassword()
    {
        $user = Auth::user();
        if(is_null($user->password)) {
            return view('user.set_password');
        } else {
            return view('user.confirm_password')->with('email', $user->email);
        }
    }

    public function confirmPassword(Request $request)
    {
        $user = Auth::user();
        $email = $user->email;

        // 입력한 비밀번호와 인증된 사용자의 비밀번호를 비교한다.
        if(Auth::attempt(['email' => $email, 'password' => $request->get('password') ], false, false)) {
            return redirect(route('user.edit'));
        } else {
            return redirect(route('user.getPasswordConfirm'))->with('message', '비밀번호가 틀립니다.');
        }
    }

    public function setPassword(Request $request)
    {
        $this->validate($request, User::$rulesSetPassword);

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();

        return redirect(route('user.edit'));
    }

    public function update(Request $request)
    {

        $this->validate($request, User::$rulesUpdate);

        $user = Auth::user();
        if($request->get('password') !== '') {
            $user->password = bcrypt($request->get('password'));
            $user->save();
        }

        // 현재 시간 date type으로 받기
        $nowDate = Carbon::now()->toDateString();

        // 추천인 닉네임 받은 것을 해당 닉네임의 id로 조회
        $recommendedId = '';
        if($request->get('recommend') !== '') {
            $recommendedUser = User::where([
                'nick' => $request->get('recommend'),
            ])->first();

            if(is_null($recommendedUser)) {
                return redirect(route('user.edit'))
                        ->withErrors(['recommend' => '추천인이 존재하지 않습니다.']);
            }
            $recommendedId = $recommendedUser->id;
        }

        $user->update([
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
            'recommend' => $recommendedId,
        ]);

        // 정보공개 체크박스에 체크를 했거나 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if($request->has('open') || $user->open != $request->get('open')) {
            $user->open = $request->get('open');
            $user->open_date = $nowDate;
            $user->save();
        }

        return redirect('/index')->with('message', $user->nick . '님의 회원정보가 변경되었습니다.');
    }

}
