<?php

namespace App\Listeners;

use App\Events\PreRegistrationSuccessEvent;
use App\Mail\PreRegistrationSucessEmail;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Product;
use App\Models\Registration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
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

        $registration = Registration::find($event->registration->registration_uid);
        $registration->reservation = true;
        $registration->reservation_valid_until = '2023-12-31';
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
        $completeregistrationlink = env("APP_URL") . '/events/' . $registration->course_uid . '/registration/' . $registration->registration_uid . '/complete';



        if (App::isProduction()) {
            Mail::to($email_adress)
                ->send(new PreRegistrationSucessEmail($registration, $products, $event, $club->name, $country->country_name_en, $startlistlink,$completeregistrationlink));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new PreRegistrationSucessEmail($registration, $products, $event, $club->name, $country->country_name_en, $startlistlink,$completeregistrationlink));
        }
    }
}
