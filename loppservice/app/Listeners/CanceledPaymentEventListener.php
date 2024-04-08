<?php

namespace App\Listeners;

use App\Events\CanceledPaymentEvent;
use App\Models\Event;
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
            $registration = Registration::find($event->registration_uid);
            $current_event = Event::where('course_uid',$registration->course_uid);

            if ($registration) {
                Log::debug("handle cancelation of payment" . $registration->registration_uid);
                if ($event->is_final_registration_on_event) {
                    Log::debug("handle cancelation of finalregistration payment" . $registration->registration_uid);
                    //go back to reservation
                    $registration->reservation_valid_until = $current_event->eventconfiguration->reservationconfig->use_reservation_until;
                    $registration->reservation = true;
                    $registration->save();

                } else {
                    $registration->delete();
                    Log::debug("handle cancelation of event registration payment" . $registration->registration_uid);
                    // delete reservation
                }
            } else {
                Log::debug("cannot find any registration with uid " . $event->registration_uid);
            }
        }
    }
}
