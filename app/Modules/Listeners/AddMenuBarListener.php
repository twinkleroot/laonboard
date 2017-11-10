<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddMenuBar;

class AddMenuBarListener
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
     * @param  AddMenuBar  $event
     * @return void
     */
    public function handle(AddMenuBar $event)
    {
        $theme = cache('config.theme')->name ? : 'default';

        echo viewDefault("$theme.layouts.menu");
    }
}
