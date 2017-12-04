<?php

namespace Modules\Visit\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Visit\Events\AddVisitStatus;
use Modules\Visit\Models\Visit;
use Modules\Visit\Models\VisitSum;
use Cookie;
use Carbon\Carbon;
use Schema;

class AddVisitStatusListener
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
     * @param  AddVisitStatus $event
     * @return void
     */
    public function handle(AddVisitStatus $event)
    {
        if(Schema::hasTable('visits') && Schema::hasTable('visit_sums')) {
            $todayDate = Carbon::now()->toDateString();
            $todayTime = Carbon::now()->toTimeString();
            $yesterdayDate = Carbon::yesterday()->toDateString();
            $request = request();

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

            $visitSums = VisitSum::all();
            // 오늘
            $visitToday = $visitSums->where('date', $todayDate)->first();
            $todayCount = number_format($visitToday ? $visitToday->count : 0);
            // 어제
            $visitYesterday = $visitSums->where('date', $yesterdayDate)->first();
            $yesterdayCount = number_format($visitYesterday ? $visitYesterday->count : 0);
            // 최대
            $visitMax = number_format($visitSums->max('count'));
            // 전체
            $visitTotal = number_format($visitSums->sum('count'));

            $params = compact(
                "todayCount", "yesterdayCount", "visitMax", "visitTotal"
            );

            echo view('modules.visit.index', $params);
        }
    }

}
