<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersEventListener
{
    /**
     * The events handled by the listener.
     *
     * @var array
     */
    public static $listensFor = [
        //
    ];

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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe(\Illuminate\Events\Dispatcher $events)
    {
        $events->listen(
            \App\Events\CreatePasswordRemind::class,
            __CLASS__ . '@onCreatePasswordRemind'
        );
    }

    public function onCreatePasswordRemind(\App\Events\CreatePasswordRemind $event)
    {
        $theme = cache('config.theme')->name ? : 'default';
        $address = cache('config.email.default')->adminEmail;
        $name = cache('config.email.default')->adminEmailName;

        $mailPath = "themes.$theme.mails.reset_email_form";
        $mailPath = view()->exists($mailPath) ? $mailPath : "themes.default.mails.reset_email_form";
        $subject = '비밀번호 재설정 메일입니다.';

        try {
            \Mail::send(
                $mailPath,
                ['token' => $event->token],
                function ($message) use ($event, $address, $subject, $name) {
                    $message->to($event->email);
                    $message->subject($subject);
                    $message->from($address, $name);
                }
            );
        } catch (Exception $e) {
            $params = [
                'token' => $event->token
            ];
            $content = \View::make($mailPath, $params)->render();

            mailer($name, $address, $event->email, $subject, $content);
        }
    }

}
