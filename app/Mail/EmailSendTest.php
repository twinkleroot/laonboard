<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class EmailSendTest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = cache('config.email.default')->adminEmail;
        $name = cache('config.email.default')->adminEmailName;
        $theme = cache('config.theme')->name ? : 'default';

        return $this->from($address, $name)
                    ->subject($this->subject)
                    ->view("themes.$theme.mails.email_send_test")
                    ->with('now', Carbon::now());
    }
}
