<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddSearchBar;

class AddSearchBarListener
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
     * @param  AddSearchBar  $event
     * @return void
     */
    public function handle(AddSearchBar $event)
    {
        $theme = cache('config.theme')->name ? : 'default';

        echo viewDefault("$theme.layouts.search");
    }
}
