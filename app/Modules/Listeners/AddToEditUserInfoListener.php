<?php

namespace App\Modules\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\Events\AddToEditUserInfo;

class AddToEditUserInfoListener
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
     * @param  AddToEditUserInfo  $event
     * @return void
     */
    public function handle(AddToEditUserInfo $event)
    {
        $config = cache('config.join');
        $theme = cache('config.theme')->name ? : 'default';
        $skin = $config->skin;
        $params = [
            'user' => auth()->user(),
            'config' => $config
        ];

        echo viewDefault("$theme.users.$skin.form_name_and_hp", $params);
    }
}
