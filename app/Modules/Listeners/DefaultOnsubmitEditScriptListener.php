<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\DefaultOnsubmitEditScript;

class DefaultOnsubmitEditScriptListener
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
     * @param  DefaultOnsubmitEditScript  $event
     * @return void
     */
    public function handle(DefaultOnsubmitEditScript $event)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.join')->skin;

        echo viewDefault("$theme.users.$skin.user_form_onsubmit");
    }
}
