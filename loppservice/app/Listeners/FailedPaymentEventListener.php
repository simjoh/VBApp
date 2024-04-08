<?php

namespace App\Listeners;

use App\Events\FailedPaymentEvent;
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
        $registration = Registration::find($event->registration_uid);
        if ($registration) {
            Log::debug("handle failed payment" . $registration->registration_uid);
            if (boolval($event->is_final_registration_on_event) == true) {
                Log::debug("Handle failed finalregistration payment on final registration" . $registration->registration_uid);
                //go back to reservation
            } else {
                Log::debug("Handle failed payment on reservation" . $registration->registration_uid);
                // send failed payment email
            }
        } else {
            Log::debug("cannot find any registration with uid " . $event->registration_uid);
        }
    }
}
