<?php

namespace App\Listeners;

use App\Events\PreRegistrationSuccessEvent;
use App\Mail\CompletedRegistrationEmail;
use App\Mail\PreRegistrationSucessEmail;
use App\Models\Registration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class PreRegistrationSuccessEventListener
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
    public function handle(PreRegistrationSuccessEvent $event): void
    {
        // sätt reservation till till false om man betalt föregistreringen.
        $registration = Registration::find($event->registration->registration_uid);
        $registration->reservation = true;
        $registration->reservation_valid_until = '2023-12-31';
        $registration->save();
        $email_adress = $registration->person->contactinformation->email;

        if (App::isProduction()) {
            Mail::to($email_adress)
                ->send(new PreRegistrationSucessEmail($event->registration, $event->optional));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new PreRegistrationSucessEmail($event->registration, $event->optional));
        }
    }
}
