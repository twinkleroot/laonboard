<?php

namespace Modules\Inform\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Inform\Events\AddNotificationMenu;

class AddNotificationMenuListener
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
     * @param  AddNotificationMenu  $event
     * @return void
     */
    public function handle(AddNotificationMenu $event)
    {
        echo view("modules.inform.inform_menu");
    }
}
