<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddLogo;

class AddLogoListener
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
     * @param  AddLogo  $event
     * @return void
     */
    public function handle(AddLogo $event)
    {
        $theme = cache('config.theme')->name ? : 'default';

        echo viewDefault("$theme.layouts.logo");
    }
}
