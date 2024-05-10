<?php

namespace App\Listeners;

use App\Events\SendEventInfoEmail;
use App\Mail\EventInfoEmail;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEventInfoEmailEventListener
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
    public function handle(SendEventInfoEmail $event): void
    {
        $registration = Registration::where('registration_uid', $event->registration_uid)->get()->first();
        $event_event = Event::where('event_uid', $event->track_uid)->get()->first();
        $person = Person::find($registration->person_uid);
        if (App::isProduction()) {
            if ($event_event->event_type === 'BRM') {
               Mail::to($person->contactinformation->email)
                    ->send(new EventInfoEmail($registration, $event_event));
            }
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new EventInfoEmail($registration, $event_event));
        }
    }
}
