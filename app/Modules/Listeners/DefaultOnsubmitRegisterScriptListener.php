<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\DefaultOnsubmitRegisterScript;

class DefaultOnsubmitRegisterScriptListener
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
     * @param  DefaultOnsubmitRegisterScript  $event
     * @return void
     */
    public function handle(DefaultOnsubmitRegisterScript $event)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $skin = cache('config.join')->skin;

        echo viewDefault("$theme.users.$skin.user_form_onsubmit");
    }
}
