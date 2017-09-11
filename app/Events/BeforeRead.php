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
use App\Write;
use App\Board;

class BeforeRead
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $writeModel;
    public $write;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, Write $writeModel, Write $write)
    {
        $this->request = $request;
        $this->writeModel = $writeModel;
        $this->write = $write;
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
