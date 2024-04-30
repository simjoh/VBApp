<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FailedPaymentEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $registration_uid;
    public bool $is_final_registration_on_event;

    /**
     * Create a new event instance.
     */
    public function __construct(string $registration_uid, bool $is_final_registration_on_event)
    {
        $this->is_final_registration_on_event = $is_final_registration_on_event;
        $this->registration_uid = $registration_uid;
        //
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
