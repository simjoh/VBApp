<?php

namespace App\Listeners;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Mail\CompletedRegistrationEmail;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        Log::debug("Sending: CompletedRegistrationSuccessEventEmail");

        // sätt reservation till till false om man betalt och är klar
        $registration = Registration::find($event->registration->registration_uid);
        $registration->reservation = false;
        $registration->reservation_valid_until = null;
        $ref_nr = mt_rand(10000, 99999);
        if (Registration::where('course_uid', $registration->course_uid)->where('ref_nr', $ref_nr)->exists()) {
            $ref_nr = mt_rand(10000, 99999);
        }

        $registration->ref_nr = $ref_nr;
        $registration->save();
        $email_adress = $registration->person->contactinformation->email;
        $event = Event::find($registration->course_uid)->get()->first();
        $products = Product::whereIn('productID', Optional::where('registration_uid', $registration->registration_uid)->select('productID')->get()->toArray())->get();
        $club = DB::table('clubs')->select('name')->where('club_uid', $registration->club_uid)->get()->first();
        $country = Country::where('country_id', $registration->person->adress->country_id)->get()->first();

        $startlistlink = env("APP_URL") .'/startlist/event/' . $registration->course_uid . '/showall';

        if (App::isProduction()) {
            Mail::to($email_adress)
                ->send(new CompletedRegistrationEmail($registration, $products, $event, $club->name, $country->country_name_en, $startlistlink));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new CompletedRegistrationEmail($registration, $products, $event, $club->name, $country->country_name_en, $startlistlink));
        }
    }
}
