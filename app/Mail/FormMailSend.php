<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FormMailSend extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $content;
    public $type;
    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $subject, $content, $type, $files)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->type = $type;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this
            ->from($this->email, $this->name)
            ->subject($this->subject);

        if(count($this->files) > 0) {
            foreach($this->files as $file) {
                $mail->attach($file->path(), [
                    'as' => $file->getClientOriginalName(),
                ]);
            }
        }
        if($this->type) {
            $mail->view('mail.default.formmail');
        } else {
            $mail->text('mail.default.formmail_plain');
        }

        return $mail;
    }
}
