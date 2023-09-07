<?php

namespace App\Listeners;

use App\Events\PreRegistrationSuccessEvent;
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
        //

      /// dd($event->registration->adress);

        Mail::to('receiverinbox@mailhog.local')
            ->send(new \App\Mail\PreRegistrationSucessEmail("Florian"));

        print_r($event);
    }
}
