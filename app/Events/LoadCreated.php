<?php

namespace App\Events;

use App\Models\Load;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LoadCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var Load
     */
    public $load;

    /**
     * Create a new event instance.
     *
     * @param Load $load
     */
    public function __construct(Load $load)
    {
        $this->load = $load;
    }

}
