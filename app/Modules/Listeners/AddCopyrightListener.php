<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddCopyright;

class AddCopyrightListener
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
     * @param  AddCopyright  $event
     * @return void
     */
    public function handle(AddCopyright $event)
    {
        $theme = cache('config.theme')->name ? : 'default';

        echo viewDefault("$theme.layouts.copyright");
    }
}
