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

class SendInformAboutComment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $writeModel;
    public $commentId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, Write $writeModel, $commentId)
    {
        $this->writeModel = $writeModel;
        $this->commentId = $commentId;
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
