<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;
use App\Contracts\BoardInterface;
use App\Contracts\WriteInterface;
use App\Models\BoardFile;

class BeforeDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $write;
    public $board;
    public $file;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, WriteInterface $write, BoardInterface $board, BoardFile $file)
    {
        $this->request = $request;
        $this->write = $write;
        $this->board = $board;
        $this->file = $file;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
