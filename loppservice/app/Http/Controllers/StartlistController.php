<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Country;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StartlistController extends Controller
{

    public function startlistFor(Request $request)
    {
        $course_uid = $request['eventuid'];
        $startlist = DB::table('registrations')
            ->join('orders', 'orders.registration_uid', '=', 'registrations.registration_uid')
            ->join('person', 'person.registration_registration_uid', '=', 'registrations.registration_uid')
            ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
            ->join('countries', 'countries.country_id', '=', 'adress.country_id')
            ->select('registrations.*', 'person.*', 'adress.*', 'countries.*', 'club.name AS club_name')
            ->where('course_uid', $course_uid)
            ->where('order_status', 'paid')
            ->get();

        return view('startlist.show', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all()]);
    }

}
