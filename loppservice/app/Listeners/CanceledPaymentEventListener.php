<?php

namespace App\Listeners;

use App\Events\CanceledPaymentEvent;
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
        $registration = Registration::find($event->registration_uid);

        if ($registration) {
            Log::debug("handle cancelation of payment" . $registration->registration_uid);
            if ($event->is_final_registration_on_event) {
                Log::debug("handle cancelation of finalregistration payment" . $registration->registration_uid);
                //go back to reservation
            } else {
                Log::debug("handle cancelation of reservation or registration payment" . $registration->registration_uid);
                // delete reservation
            }
        } else {
            Log::debug("cannot find any registration with uid " . $event->registration_uid);
        }


    }
}
