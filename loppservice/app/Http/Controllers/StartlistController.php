<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class StartlistController extends Controller
{

    public function startlistFor(Request $request)
    {
        $course_uid = $request['courseUid'];

        Registration::find($course_uid);
        return view('startlist.show', ['startlista' => 'Lista']);
    }
    //
}
