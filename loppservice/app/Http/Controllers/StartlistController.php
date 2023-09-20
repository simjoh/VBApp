<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Country;
use App\Models\Registration;
use Illuminate\Http\Request;

class StartlistController extends Controller
{

    public function startlistFor(Request $request)
    {
        $course_uid = $request['eventuid'];
        $registrations = Registration::where('course_uid',$course_uid)->get();
        $registrations->test = "ssssssssssss";
        return view('startlist.show', ['startlista' => $registrations, 'countries' => Country::all(), 'clubs' => Club::all()]);
    }

}
