<?php

namespace App\Listeners;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Mail\CompletedRegistrationEmail;
use App\Models\Club;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Support\Facades\App;
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
        // sätt reservation till till false om man betalt och är klar
        $registration = Registration::find($event->registration->registration_uid);
        $registration->reservation = false;
        $registration->reservation_valid_until = null;
        $registration->save();
        $email_adress = $registration->person->contactinformation->email;
        $event = Event::find($registration->course_uid)->get()->first();
        $products = Product::whereIn('productID', Optional::where('registration_uid', $registration->registration_uid)->select('productID')->get()->toArray())->get();
        $club = Club::find($registration->club_uid)->select('name')->get()->first();

        if (App::isProduction()) {
            Mail::to($email_adress)
                ->send(new CompletedRegistrationEmail($registration, $products, $event, $club));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new CompletedRegistrationEmail($registration, $products, $event, $club));
        }
    }
}
