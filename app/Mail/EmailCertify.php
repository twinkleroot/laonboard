<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class EmailCertify extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $nick;
    public $isEmailChange;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $nick, $isEmailChange)
    {
        $this->user = $user;
        $this->nick = $nick;
        $this->isEmailChange = $isEmailChange;
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

        return $this
            ->from($address, $name)
            ->subject('['. cache('config.homepage')->title. '] 인증확인 메일입니다.')
            ->view('mail.default.email_certify')
            ->with([
                'nick' => $this->nick,
                'url' => $url,
                'isEmailChange' => $this->isEmailChange
            ]);
    }
}
