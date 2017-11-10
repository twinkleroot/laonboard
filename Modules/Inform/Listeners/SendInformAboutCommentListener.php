<?php

namespace Modules\Inform\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inform\Events\SendInformAboutComment;
use Modules\Inform\Models\Inform;

class SendInformAboutCommentListener
{
    public $inform;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Inform $inform)
    {
        $this->inform = $inform;
    }

    /**
     * Handle the event.
     *
     * @param  SendInformAboutComment  $event
     * @return void
     */
    public function handle(SendInformAboutComment $event)
    {
        // 알림 전송
        $this->inform->sendInform($event->writeModel, $event->commentId);
    }
}
