<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Point;

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

        // 당일 첫 로그인 포인트 주기.
        $rel_table = '@login';
        $rel_email = $event->user->email;
        $rel_action = $nowDate;

        // 기존에 같은 건으로 포인트를 받았는지 조회. 조회되면 포인트 적립 불가
        $existPoint = Point::checkPoint($rel_table, $rel_email, $rel_action);
        // 회원 가입인 경우 로그인 포인트를 부여하지 않음.
        $isUserJoin = Point::isUserJoin($event->user);
        
        if($isUserJoin == false && is_null($existPoint)) {
            $content = $nowDate . ' 첫 로그인';
            $pointToGive = Point::pointType('login');
            $event->user->point = $event->user->point + $pointToGive;       // 당일 첫 로그인 포인트 부여
            Point::loggingPoint($event->user, $pointToGive, $rel_table, $rel_action, $content);     // 포인트 내역 기록
        }

        $event->user->save();
    }

}
