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

        $nickDiff = $current->diffInDays($user->nick_date);
        $nickChangable = false;
        if($nickDiff > config('gnu.nickDate')) {
            $nickChangable = true;
        }

        $openChangable = false;
        $dueDate = $current;
        $openDate = $user->open_date;
        // dump($current->diffInDays($openDate));
        if(is_null($openDate)) {
            $openChangable = true;
        } else {
            $openDiff = $current->diffInDays($openDate);
            if($openDiff >= config('gnu.openDate')) {
                $openChangable = true;
            }
            $dueDate = $openDate->addDays(config('gnu.openDate'));
        }

        return view('user.edit')
            ->with('user', $user)
            ->with('nickChangable', $nickChangable)
            ->with('openChangable', $openChangable)
            ->with('dueDate', $dueDate);
            ;
    }

    public function getPasswordConfirm()
    {
        $user = Auth::user();
        return view('user.password_confirm')->with('email', $user->email);
    }

    public function postPasswordConfirm(Request $request)
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

    public function update(Request $request)
    {

        $this->validate($request, User::$rulesUpdate);

        $user = Auth::user();
        if($request->get('password') !== '') {
            $user->password = bcrypt($request->get('password'));
            $user->save();
        }

        $nowDate = Carbon::now()->toDateString();

        $user->update([
            'nick' => !is_null($request->get('nick')) ? $request->get('nick') : $user->nick,
            'nick_date' => !is_null($request->get('nick')) ? $nowDate : $user->nick_date,
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
            'mailing' => !is_null($request->get('mailing')) ? $request->get('mailing') : 0,
            'sms' => !is_null($request->get('sms')) ? $request->get('sms') : 0,
        ]);

        // open입력값이 널이 아니고 기존에 open값과 open입력값이 다르다면 기존 open 값에 open 입력값을 넣는다.
        if(!is_null($request->get('open')) && $user->open != $request->get('open')) {
            $user->open = $request->get('open');
            $user->open_date = $nowDate;
            $user->save();
        }


        return redirect('/index')->with('message', $user->nick . '님의 회원정보가 변경되었습니다.');
    }

}
