<?php

namespace App\Listeners;

use App\Events\PreRegistrationSuccessEvent;
use App\Mail\PreRegistrationSucessEmail;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Person;
use App\Models\Product;
use App\Models\Registration;
use App\Models\StartNumberConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PreRegistrationSuccessEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct(){}

    /**
     * Handle the event.
     */
    public function handle(PreRegistrationSuccessEvent $event): void
    {
        $registration = Registration::where('registration_uid',$event->registration->registration_uid)->first();
        if (!$registration) {
            return; // Exit if registration not found
        }
        $event_event = Event::where('event_uid',$registration->course_uid)->first();
        $registration->reservation = true;
        $registration->reservation_valid_until = $event_event->eventconfiguration->reservationconfig->use_reservation_until;
        $ref_nr = mt_rand(10000, 99999);
        if (Registration::where('course_uid', $registration->course_uid)->where('ref_nr', $ref_nr)->exists()) {
            $ref_nr = mt_rand(10000, 99999);
        }

        $person = Person::find($registration->person_uid);
        $registration->ref_nr = $ref_nr;
        $email_adress = $person->contactinformation->email;

        $products = Product::whereIn('productID', Optional::where('registration_uid', $registration->registration_uid)->select('productID')->get()->toArray())->get();
        $club = DB::table('clubs')->select('name')->where('club_uid', $registration->club_uid)->get()->first();
        $country = Country::where('country_id', $person->adress->country_id)->get()->first();
        $collection = collect($event_event->eventconfiguration->products);
        $resevation_product = $collection->where('categoryID', 7)->first();
        $startlistlink = env("APP_URL") . '/startlist/event/' . $registration->course_uid . '/showall';
        $completeregistrationlink = env("APP_URL") . '/events/' . $registration->course_uid . '/registration/' . $registration->registration_uid . '/msrcomplete?productID=' . $resevation_product->productID;

        $updatedetaillink = env("APP_URL") . '/events/' . $registration->course_uid . '/registration/' . $registration->registration_uid . '/getregitration';


        $registration->startnumber = $this->getStartnumber($event_event->event_uid, $event_event->eventconfiguration->startnumberconfig);
        $registration->save();


        if (App::isProduction()) {
            Mail::to($email_adress)->cc('no-reply@randonneurslaponia.se')
                ->send(new PreRegistrationSucessEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $completeregistrationlink, $updatedetaillink, $person));
        } else {
            Mail::to('receiverinbox@mailhog.local')
                ->send(new PreRegistrationSucessEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $completeregistrationlink, $updatedetaillink, $person));
        }
    }


    private function getStartnumber(string $course_uid, StartNumberConfig $startNumberConfig): int
    {
        $current_max = Registration::where('course_uid', $course_uid)->max('startnumber');
        if ($current_max == null) {
            return $startNumberConfig->begins_at;
        }
        $startnumbers = Registration::where('course_uid', $course_uid)->whereNotNull('startnumber')->pluck('startnumber')->values();
        $arrwithstartnumbers = collect($startnumbers);
        $missingnumber = array_diff(range($startNumberConfig->begins_at, $current_max), $arrwithstartnumbers->toArray());
        $valtogetfromarray = array_key_first($missingnumber);
        if ($valtogetfromarray) {
            return $missingnumber[$valtogetfromarray];
        }
        return $current_max + $startNumberConfig->increments;
    }
}
