<?php

namespace App\Listeners;

use App\Events\CanceledPaymentEvent;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;

class CanceledPaymentEventListener
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
    public function handle(CanceledPaymentEvent $event): void
    {

        if ($event->isnonparticipantorder) {
            Log::debug("handle cancelation of nonparticipant payment" . $event->registration_uid);
        } else {

            $registration = Registration::where('registration_uid', $event->registration_uid)->get()->first();
            $current_event = Event::where('event_uid', $registration->course_uid)->get()->first();

            if ($registration) {
                Log::debug("handle cancelation of payment" . $registration->registration_uid);
                if ($event->is_final_registration_on_event) {
                    Log::debug("handle cancelation of finalregistration payment" . $registration->registration_uid);
                    //go back to reservation
                    $registration->reservation_valid_until = $current_event->eventconfiguration->reservationconfig->use_reservation_until;
                    $registration->reservation = true;
                    $registration->save();
                } else {
                    $person = Person::where('person_uid', $registration->person_uid)->get()->first();
                    if (count($person->registration) === 1) {
                        $registration->delete();
                        $person->delete();
                    } else {
                        $registration->delete();
                    }
                    Log::debug("handle cancelation of event registration payment" . $registration->registration_uid);
                }
            } else {
                Log::debug("cannot find any registration with uid " . $event->registration_uid);
            }
        }
    }
}
