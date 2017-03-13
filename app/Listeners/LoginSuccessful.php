<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class LoginSuccessful
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
     * @param  Login  $event
     */
    public function handle(Login $event)
    {
        $event->user->today_login = Carbon::now();
        // dump($event->user->today_login);
        // $event->user->ip = $event->request->ip();

        $event->user->save();
    }
}
