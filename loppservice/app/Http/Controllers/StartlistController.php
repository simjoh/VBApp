<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Country;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StartlistController extends Controller
{
    public function startlistFor(Request $request)
    {
        $course_uid = $request['eventuid'];
        $use_stripe = env("USE_STRIPE_PAYMENT_INTEGRATION");
        $event = Event::where('event_uid', $request['eventuid'])->get()->first();

        if($event->eventconfiguration->use_stripe_payment) {
            $startlist = DB::table('registrations')
                ->join('orders', 'orders.registration_uid', '=', 'registrations.registration_uid')
                ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
                ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
                ->join('countries', 'countries.country_id', '=', 'adress.country_id')
                ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
                ->select(
                    'registrations.registration_uid',
                    'registrations.course_uid',
                    'registrations.startnumber',
                    'registrations.additional_information',
                    'person.firstname',
                    'person.surname',
                    'person.birthdate',
                    'adress.city',
                    'countries.country_name_en',
                    'countries.country_name_sv',
                    'countries.country_code',
                    'countries.flag_url_svg',
                    'countries.flag_url_png',
                    'clubs.name AS club_name'
                )
                ->where('registrations.course_uid', $course_uid)
                ->where('orders.payment_status', 'paid')
                ->orderBy('person.surname')
                ->orderBy('person.firstname')
                ->orderBy('registrations.startnumber')
                ->distinct()
                ->get();
        } else {
            $startlist = DB::table('registrations')
                ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
                ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
                ->join('countries', 'countries.country_id', '=', 'adress.country_id')
                ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
                ->select(
                    'registrations.registration_uid',
                    'registrations.course_uid',
                    'registrations.startnumber',
                    'registrations.additional_information',
                    'person.firstname',
                    'person.surname',
                    'person.birthdate',
                    'adress.city',
                    'countries.country_name_en',
                    'countries.country_name_sv',
                    'countries.country_code',
                    'countries.flag_url_svg',
                    'countries.flag_url_png',
                    'clubs.name AS club_name'
                )
                ->where('registrations.course_uid', $course_uid)
                ->where('registrations.reservation', 0)
                ->orderBy('person.surname')
                ->orderBy('person.firstname')
                ->orderBy('registrations.startnumber')
                ->distinct()
                ->get();
        }

        if ($event->event_type === 'BRM' || $event->event_type === 'BP') {
            return view('startlist.brmshow', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all(), 'event' => $event]);
        } else {
            return view('startlist.show', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all()]);
        }
    }
}
