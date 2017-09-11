<?php

namespace App\Listeners;

use \App\Events\BeforeRead;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class BeforeReadListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  BeforeRead  $event
     * @return void
     */
    public function handle(BeforeRead $event)
    {
        $hit = $event->write->hit;
        $user = auth()->user();
        $userId = !$user ? 0 : $user->id;
        $userHash = !$user ? '' : $user->id_hashkey;
        $sessionName = "session_view_". $event->writeModel->getTable(). '_'. $event->write->id. '_'. $userHash;
        if(!session()->get($sessionName) && $userId != $event->write->user_id) {
            // 조회수 증가
            $hit = $event->writeModel->increaseHit($event->writeModel, $event->write);
            // 포인트 계산(차감)
            $event->writeModel->calculatePoint($event->write, $event->request, 'read');

            session()->put($sessionName, true);
        }
    }
}
