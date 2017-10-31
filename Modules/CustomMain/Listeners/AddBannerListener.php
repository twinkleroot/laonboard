<?php

namespace Modules\CustomMain\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\CustomMain\Events\AddBanner;

class AddBannerListener
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
     * @param  AddBanner  $event
     * @return void
     */
    public function handle(AddBanner $event)
    {
        $subject = '배너 제목';
        $content = '배너 내용입니다.';
        $imgPath = 'images/banner.jpg';

        echo view('modules.custommain.banner',[
            'subject' => $subject,
            'content' => $content,
            'imgPath' => $imgPath,
        ]);
    }

}
