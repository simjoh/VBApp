<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;




class FailedParticipantTransferEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $registration_uid;
    public string $error_uid;

    /**
     * Create a new event instance.
     */
    public function __construct(string $registration_uid, string $error_uid)
    {
        $this->error_uid = $error_uid;
        $this->registration_uid = $registration_uid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
