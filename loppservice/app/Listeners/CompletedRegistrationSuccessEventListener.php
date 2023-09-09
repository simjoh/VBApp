<?php

namespace App\Listeners;

use App\Events\CompletedRegistrationSuccessEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CompletedRegistrationSuccessEventListener
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
    public function handle(CompletedRegistrationSuccessEvent $event): void
    {

        Mail::to('receiverinbox@mailhog.local')
            ->send(new \App\Mail\CompletedRegistrationEmail($event->registration));
        //
       // dd($event->registration);
    }
}
