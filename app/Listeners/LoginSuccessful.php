<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Point;
use App\Config;

class LoginSuccessful
{

    public $request;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $nowDate = Carbon::now()->toDateString();

        // 로그인한 시간, 로그인 한 지점의 IP 저장.
        $event->user->today_login = Carbon::now();
        $event->user->login_ip = $this->request->ip();

        $event->user->save();

        // 관리자 임을 세션에 등록
        if($event->user->level == 10) {
            session()->put('admin', true);
        }

        // 회원 가입인 경우($isUserJoin == true) 로그인 포인트를 부여하지 않음.
        if( !Point::isUserJoin($event->user) ) {
            // 당일 첫 로그인 포인트 부여
            Point::addPoint([
                'user' => $event->user,
                'relTable' => '@login',
                'relEmail' => $event->user->email,
                'relAction' => $nowDate,
                'content' => $nowDate . ' 첫 로그인',
                'type' => 'login',
            ]);
        }

    }

}
