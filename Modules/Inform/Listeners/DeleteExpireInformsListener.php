<?php

namespace Modules\Inform\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inform\Events\DeleteExpireInforms;

class DeleteExpireInformsListener
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
     * @param  DeleteExpireInforms  $event
     * @return void
     */
    public function handle(DeleteExpireInforms $event)
    {
        // 유효기간이 만료된 알림 삭제
        $subDays = cache('config.inform')->del ? : config('inform.del');
        $informs = auth()->user()->notifications
                ->where('created_at', '<', \Carbon\Carbon::now()->subDays($subDays));

        foreach($informs as $inform) {
            $inform->delete();
        }
    }
}
