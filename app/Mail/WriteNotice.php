<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WriteNotice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailSubject;
    public $writeSubject;
    public $name;
    public $content;
    public $linkUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailSubject, $writeSubject, $name, $content, $linkUrl)
    {
        $this->mailSubject = $mailSubject;
        $this->writeSubject = $writeSubject;
        $this->name = $name;
        $this->content = $content;
        $this->linkUrl = $linkUrl;
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

        return $this
            ->from($address, $name)
            ->subject($this->mailSubject)
            ->view("themes.$theme.mails.write_notice")
            ->with([
                'subject' => $this->writeSubject,
                'name' => $this->name,
                'content' => $this->content,
                'linkUrl' => $this->linkUrl,
            ]);
    }
}
