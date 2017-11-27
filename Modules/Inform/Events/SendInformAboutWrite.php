<?php

namespace Modules\Inform\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;
use App\Models\Write;

class SendInformAboutWrite
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $writeModel;
    public $writeId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, Write $writeModel, $writeId)
    {
        $this->writeModel = $writeModel;
        $this->writeId = $writeId;
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
