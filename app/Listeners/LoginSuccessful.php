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

        // 당일 첫 로그인 포인트 부여
        Point::addPoint([
            'user' => $event->user,
            'relTable' => '@login',
            'relEmail' => $event->user->email,
            'relAction' => $nowDate,
            'content' => $nowDate . ' 첫 로그인',
            'type' => 'login',
        ]);

        $event->user->save();
    }

}
