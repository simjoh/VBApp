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

        if($use_stripe) {
            $startlist = DB::table('registrations')
                ->join('orders', 'orders.registration_uid', '=', 'registrations.registration_uid')
                ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
                ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
                ->join('countries', 'countries.country_id', '=', 'adress.country_id')
                ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
                ->select('registrations.*', 'person.*', 'adress.*', 'countries.*', 'clubs.name AS club_name')
                ->where('course_uid', $course_uid)->where('reservation', 0)
                ->where('payment_status', 'paid')->orderBy("surname")->distinct()
                ->get();

        } else {
            $startlist = DB::table('registrations')
                ->join('orders', 'orders.registration_uid', '=', 'registrations.registration_uid')
                ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
                ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
                ->join('countries', 'countries.country_id', '=', 'adress.country_id')
                ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
                ->select('registrations.*', 'person.*', 'adress.*', 'countries.*', 'clubs.name AS club_name')
                ->where('course_uid', $course_uid)->where('reservation', 0)->orderBy("surname")->distinct()
                ->get();

        }
        $event = Event::where('event_uid', $request['eventuid'])->get()->first();
        if ($event->event_type === 'BRM') {
            return view('startlist.brmshow', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all(), 'event' => $event]);
        } else {
            return view('startlist.show', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all()]);
        }
    }
}
