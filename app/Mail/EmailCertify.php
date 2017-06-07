<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailCertify extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = route('emailCertify', [
                'id' => $this->user->id_hashkey,
                'crypt' => $this->user->email_certify2
            ]);
        return $this->subject('[' . config('app.name') . '] 인증확인 메일입니다.')
                    ->view('user.email_certify')
                    ->with('url', $url);
    }
}
