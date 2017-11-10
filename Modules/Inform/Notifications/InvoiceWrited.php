<?php

namespace Modules\Inform\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Board;
use DB;

class InvoiceWrited extends Notification implements ShouldQueue
{
    use Queueable;

    public $board;
    public $writeId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Board $board, $writeId)
    {
        $this->board = $board;
        $this->writeId = $writeId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $write = DB::table('write_'. $this->board->table_name)->find($this->writeId);
        $parent = $write;
        if($write->is_comment) {
            $parent = DB::table('write_'. $this->board->table_name)->find($write->parent);
        }

        return [
            'tableName' => $this->board->table_name,
            'writeId' => $write->id,
            'parentId' => $write->parent,
            'reply' => $write->reply ? : '',
            'isComment' => $write->is_comment,
            'subject' => $write->subject ? : $write->content,
            'parentSubject' => $parent->subject,
            'writeUser' => $write->user_id,
            'writeCreatedAt' => $write->created_at,
        ];
    }
}
