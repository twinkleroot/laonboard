<?php

namespace App\Listeners;

use App\Events\GetRssView;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GetRssViewListener
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
     * @param  GetRssView  $event
     * @return void
     */
    public function handle(GetRssView $event)
    {
        if($event->board->read_level >= 2) {
            abort(500, '비회원 읽기가 가능한 게시판만 RSS 지원합니다.');
        }
        if(!$event->board->use_rss_view) {
            abort(500, 'RSS 보기가 금지되어 있습니다.');
        }
    }
}
