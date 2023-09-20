<?php

namespace App\Events;

use App\Models\Registration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreRegistrationSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Registration $registration;
    public Collection $optional;

    /**
     * Create a new event instance.
     */
    public function __construct(Registration $registration, Collection $optional)
    {
        //
        $this->registration = $registration;
        $this->optional = $optional;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reserved-registration'),
        ];
    }
}


