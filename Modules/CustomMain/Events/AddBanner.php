<?php

namespace Modules\CustomMain\Events;

use Illuminate\Queue\SerializesModels;

class AddBanner
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn()
    {
    }
}
