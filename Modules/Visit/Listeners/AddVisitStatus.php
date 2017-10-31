<?php

namespace Modules\Visit\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Visit\Events\PushVisitStatus;
use Modules\Visit\Models\Visit;
use Modules\Visit\Models\VisitSum;
use Cookie;
use Carbon\Carbon;

class AddVisitStatus
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PushVisitStatus $event
     * @return void
     */
    public function handle(PushVisitStatus $event)
    {
        $todayDate = Carbon::now()->toDateString();
        $todayTime = Carbon::now()->toTimeString();
        $yesterdayDate = Carbon::yesterday()->toDateString();
        // $request = $event->request;
        $request = request();

        // if(pickupCookie('visit_ip') != $request->server('REMOTE_ADDR')) {
        //     $cookie = makeCookie('visit_ip', $request->server('REMOTE_ADDR'), 1440);

            $remoteAddr = $request->server('REMOTE_ADDR');
            $referer = null;
            if (null !== $request->server('HTTP_REFERER')) {
                $referer = $request->server('HTTP_REFERER');
            }
            $userAgent = $request->server('HTTP_USER_AGENT');
            $browser = null;
            $os = null;
            $device = null;

            $result = 0;
            if(! Visit::where(['ip'=>$remoteAddr, 'date'=>$todayDate])->first()) {
                $result = Visit::insertGetId([
                    'ip' => $remoteAddr,
                    'date' => $todayDate,
                    'time' => $todayTime,
                    'referer' => $referer,
                    'agent' => $userAgent,
                    'browser' => $browser,
                    'os' => $os,
                    'device' => $device,
                ]);
            }

            if($result) {
                if(VisitSum::where('date', $todayDate)->first()) {
                    VisitSum::where('date', $todayDate)->increment('count');
                } else {
                    VisitSum::insert([
                        'date' => $todayDate,
                        'count' => 1
                    ]);
                }
            }
        // }

        $visitSums = VisitSum::all();
        // 오늘
        $visitToday = $visitSums->where('date', $todayDate)->first();
        $todayCount = $visitToday ? $visitToday->count : 0;
        // 어제
        $visitYesterday = $visitSums->where('date', $yesterdayDate)->first();
        $yesterdayCount = $visitYesterday ? $visitYesterday->count : 0;
        // 최대
        $visitMax = $visitSums->max('count');
        // 전체
        $visitTotal = $visitSums->sum('count');

        echo
            "<section id=\"visit\">
                <div class=\"container\">
                    <h2>접속자집계</h2>
                    <dl>
                        <dt>오늘</dt>
                        <dd>". $todayCount. "</dd>
                        <dt>어제</dt>
                        <dd>". $yesterdayCount. "</dd>
                        <dt>최대</dt>
                        <dd>". $visitMax. "</dd>
                        <dt>전체</dt>
                        <dd>". $visitTotal. "</dd>
                    </dl>
                </div>
            </section>";
    }

}
