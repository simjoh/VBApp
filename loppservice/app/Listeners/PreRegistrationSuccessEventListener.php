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

        Mail::to('receiverinbox@mailhog.local')
            ->send(new \App\Mail\PreRegistrationSucessEmail("ssssssssssssssssssssssssssssss"));

        print_r($event);
    }
}
