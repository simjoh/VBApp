<?php

namespace App\Listeners;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\CreateParticipantInCyclingAppEvent;
use App\Mail\BRMCompletedRegistrationEmail;
use App\Mail\CompletedRegistrationEmail;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Person;
use App\Models\Product;
use App\Models\Registration;
use App\Models\StartNumberConfig;
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
    }

    /**
     * Handle the event.
     */
    public function handle(CompletedRegistrationSuccessEvent $event): void
    {
        Log::debug("Handling: CompletedRegistrationSuccessEvent " . $event->registration->registration_uid);

        // sätt reservation till till false om man betalt och är klar
        $registration = Registration::where('registration_uid', $event->registration->registration_uid)->get()->first();
        $registration->reservation = false;
        $registration->reservation_valid_until = null;
        Log::debug("Handling: CompletedRegistrationSuccessEvent " . $registration);
        Log::debug("Handling: CompletedRegistrationSuccessEvent " . $registration);
        if (!$registration->ref_nr) {
            $ref_nr = mt_rand(10000, 99999);
            if (Registration::where('course_uid', $registration->course_uid)->where('ref_nr', $ref_nr)->exists()) {
                $ref_nr = mt_rand(10000, 99999);
            }
            $registration->ref_nr = $ref_nr;
        }

        $registration->save();
        $person = Person::find($registration->person_uid);
        $email_adress = $person->contactinformation->email;
        $event_event = Event::where('event_uid', $registration->course_uid)->get()->first();
        $products = Product::whereIn('productID', Optional::where('registration_uid', $registration->registration_uid)->select('productID')->get()->toArray())->get();
        $club = DB::table('clubs')->select('name')->where('club_uid', $registration->club_uid)->get()->first();
        $country = Country::where('country_id', $person->adress->country_id)->get()->first();

        $startlistlink = env("APP_URL") . '/startlist/event/' . $registration->course_uid . '/showall';
        $updatedetaillink = env("APP_URL") . '/events/' . $registration->course_uid . '/registration/' . $registration->registration_uid . '/getregitration';

        if (!$registration->startnumber) {
            $registration->startnumber = $this->getStartnumber($event_event->event_uid, $event_event->eventconfiguration->startnumberconfig);
            $registration->save();
        }


        if (App::isProduction()) {
            if ($event_event->event_type === 'BRM') {
                Log::debug("Sending: MSR CompletedRegistrationSuccessEventEmail " . $registration->registration_uid . " " . "New Startnumber" . $registration->startnumber);
                Mail::to($email_adress)
                    ->send(new BRMCompletedRegistrationEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $updatedetaillink, $person));
            } else {
                Log::debug("Sending: MSR CompletedRegistrationSuccessEventEmail " . $registration->registration_uid . " " . "New Startnumber" . $registration->startnumber);
                Mail::to($email_adress)
                    ->send(new CompletedRegistrationEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $updatedetaillink, $person));
            }
        } else {
            if ($event_event->event_type === 'BRM') {
                Log::debug("Sending: BRM CompletedRegistrationSuccessEventEmail " . $registration->registration_uid . " " . "New Startnumber" . $registration->startnumber);
                Mail::to('receiverinbox@mailhog.local')
                    ->send(new BRMCompletedRegistrationEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $updatedetaillink, $person));
            } else {
                Log::debug("Sending: CompletedRegistrationSuccessEventEmail " . $registration->registration_uid . " " . "New Startnumber" . $registration->startnumber);
                Mail::to('receiverinbox@mailhog.local')
                    ->send(new CompletedRegistrationEmail($registration, $products, $event_event, $club->name, $country->country_name_en, $startlistlink, $updatedetaillink, $person));
            }
        }

        $create_participant_in_app = env("CREATE_PARTICIPANT_IN_CYCLING_APP");
        if ($create_participant_in_app) {
            if ($event_event->event_type === 'BRM') {
                event(new CreateParticipantInCyclingAppEvent($event_event->event_uid, $person->person_uid, $registration->registration_uid));
            }
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
