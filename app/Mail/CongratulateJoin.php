<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class CongratulateJoin extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $subject)
    {
        $this->user = $user;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = route('user.email.certify', [
            'id' => $this->user->id_hashkey,
            'crypt' => $this->user->email_certify2
        ]);

        $address = cache('config.email.default')->adminEmail;
        $name = cache('config.email.default')->adminEmailName;
        $theme = cache('config.theme')->name ? : 'default';

        return $this
            ->from($address, $name)
            ->subject($this->subject)
            ->view("themes.$theme.mails.congratulate_join")
            ->with([
                'user' => $this->user,
                'url' => $url
            ]);
    }
}
