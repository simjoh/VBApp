<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateParticipantInCyclingAppEvent
{

    public string $track_uid;
    public string $person_uid;
    public string $registration_uid;


    public function __construct(string $track_uid, string $person_uid, string $registration_uid)
    {
        $this->track_uid = $track_uid;
        $this->person_uid = $person_uid;
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
            new PrivateChannel('create_participant'),
        ];
    }

}
