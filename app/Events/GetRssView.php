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

class GetRssView
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $board;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, BoardInterface $board)
    {
        $this->request = $request;
        $this->board = $board;
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
