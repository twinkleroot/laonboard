<?php

namespace Modules\GoogleRecaptcha\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\GoogleRecaptcha\Events\AddRecaptchaClient;

class EventListener
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
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        // 리캡챠 클라이언트 부분 추가
        $events->listen(
            AddRecaptchaClient::class,
            __CLASS__. '@addRecaptchaClient'
        );
    }

    /**
     * 리캡챠 클라이언트 부분 추가
     *
     * @param AddRecaptchaClient $event
     */
    public function addRecaptchaClient(AddRecaptchaClient $event)
    {
        $params = [];
        if(request()->segments()[0] == 'bbs') {
            $params = [
                'board' => \App\Models\Board::getBoard(request()->segments()[1], 'table_name')
            ];
        }

        echo view('modules.googlerecaptcha.client', $params);
    }

}
