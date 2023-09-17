<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StartlistController extends Controller
{

    public function startlistFor(Request $request)
    {
        $course_uid = $request['courseUid'];
        return view('startlist.show', ['startlista' => 'Lista']);
    }
    //
}
