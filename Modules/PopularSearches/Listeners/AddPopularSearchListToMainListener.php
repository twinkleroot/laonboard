<?php

namespace Modules\PopularSearches\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use Modules\PopularSearches\Models\Popular;
use DB;
use Schema;

class AddPopularSearchListToMainListener
{
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if(Schema::hasTable('populars')) {
            // 인기검색어 출력
            $dateCnt = 3;   // $dateCnt : 몇일 동안
            $popCnt = 7;    // $popCnt : 검색어 몇개
            $from = Carbon::now()->subDays($dateCnt)->format("Y-m-d");
            $to = Carbon::now()->toDateString();
            $populars = Popular::select('word', DB::raw('count(*) as cnt'))
            ->whereBetween('date', [$from, $to])
            ->groupBy('word')
            ->orderBy('cnt', 'desc')
            ->orderBy('word')
            ->limit($popCnt)
            ->get();

            $params = ['populars' => $populars];

            echo view('modules.popularsearches.list', $params);
        }
    }
}
