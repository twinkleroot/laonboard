<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Point;
use App\Models\Memo;

class LoginEventListener
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

        // 관리자로 세션에 등록
        if($event->user->isAdmin()) {
            session()->put('admin', true);
        } else {
            session()->put('admin', false);
        }

        // 보낸 쪽지, 받은 쪽지가 기본환경설정에서 설정한 기간이 지나면 삭제되도록.
        Memo::orWhere('recv_user_id', $event->user->id)
            ->where('send_user_id', $event->user->id)
            ->where('send_timestamp', '<', Carbon::now()->subDays(cache('config.homepage')->memoDel))
            ->delete();

        // 회원 가입인 경우($isUserJoin == true) 로그인 포인트를 부여하지 않음.
        if( !$this->isUserJoin($event->user) ) {
            // 당일 첫 로그인 포인트 부여
            insertPoint($event->user->id, cache("config.homepage")->loginPoint, $nowDate . ' 첫 로그인', '@login', $event->user->email, $nowDate);
        }

        // 소셜 로그인으로 로그인할 때 생기는 세션 해제
        session()->forget('userFromSocial');

        fireEvent('afterLogin');
    }

    // 회원 가입 후 로그인 시키는 상태인지 검사
    private function isUserJoin($user)
    {
        $point = Point::where('user_id', $user->id)
                ->whereRaw('date(datetime) = date(CURRENT_DATE())')     // 다음날부터 로그인시 로그인 포인트 받음
                ->orderBy('id', 'desc')
                ->first();

        if(str_contains($point['content'], '회원가입')) {
            return true;
        }

        return false;
    }

}
