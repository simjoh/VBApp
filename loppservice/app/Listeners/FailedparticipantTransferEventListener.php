<?php

namespace App\Listeners;

use App\Events\FailedParticipantTransferEvent;
use App\Mail\FailedParticipantTransferEmail;
use App\Models\ErrorEvents;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class FailedparticipantTransferEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(FailedParticipantTransferEvent $event): void
    {
        $registration = Registration::find($event->registration_uid);
        $errorevent = ErrorEvents::find($event->error_uid);
        $person = Person::find($registration->person_uid);
        $event = Event::find($registration->course_uid)->get()->first();

        if (App::isProduction()) {
            Mail::to('bethem92@gmail.com')
                ->send(new FailedParticipantTransferEmail($person, $registration, $errorevent, $event));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new FailedParticipantTransferEmail($person, $registration, $errorevent, $event));
        }
    }
}
