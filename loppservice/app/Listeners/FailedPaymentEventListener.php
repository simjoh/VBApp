<?php

namespace App\Listeners;

use App\Events\FailedPaymentEvent;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Support\Facades\Log;

class FailedPaymentEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct(){}

    /**
     * Handle the event.
     */
    public function handle(FailedPaymentEvent $event): void
    {
        $registration = Registration::where('registration_uid', $event->registration_uid)->get()->first();
        $current_event = Event::where('course_uid',$registration->course_uid)->get()->first();
        if ($registration) {
            Log::debug("handle failed payment" . $registration->registration_uid);
            if (boolval($event->is_final_registration_on_event) == true) {
                Log::debug("handle failed payment of finalregistration payment" . $registration->registration_uid);
                //go back to reservation
                $registration->reservation_valid_until = $current_event->eventconfiguration->reservationconfig->use_reservation_until;
                $registration->reservation = true;
                $registration->save();
                //go back to reservation
            } else {
                $person = Person::where('person_uid', $registration->person_uid)->get()->first();
                if (count($person->registration) === 1) {
                    $registration->delete();
                    $person->delete();
                } else {
                    $registration->delete();
                }
                Log::debug("Handle failed payment on reservation" . $registration->registration_uid);
                $registration->delete();
                // send failed payment email
            }
        } else {
            Log::debug("cannot find any registration with uid " . $event->registration_uid);
        }
    }
}
