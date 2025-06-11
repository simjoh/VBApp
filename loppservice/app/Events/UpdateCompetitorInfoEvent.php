<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateCompetitorInfoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $registration_uid;
    public string $person_uid;

    public function __construct(string $registration_uid, string $person_uid)
    {
        $this->registration_uid = $registration_uid;
        $this->person_uid = $person_uid;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('update_competitor_info'),
        ];
    }
}
