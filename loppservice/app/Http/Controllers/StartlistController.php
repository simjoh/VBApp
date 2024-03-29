<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StartlistController extends Controller
{

    public function startlistFor(Request $request)
    {
        $course_uid = $request['eventuid'];
        $startlist = DB::table('registrations')
            ->join('orders', 'orders.registration_uid', '=', 'registrations.registration_uid')
            ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
            ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
            ->join('countries', 'countries.country_id', '=', 'adress.country_id')
            ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
            ->select('registrations.*', 'person.*', 'adress.*', 'countries.*', 'clubs.name AS club_name')
            ->where('course_uid', $course_uid)->where('reservation',0)
            ->where('payment_status', 'paid')->orderBy("surname")->distinct()
            ->get();

        return view('startlist.show', ['startlista' => $startlist, 'countries' => Country::all(), 'clubs' => Club::all()]);
    }

}
