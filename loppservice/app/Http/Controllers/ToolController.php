<?php

namespace App\Http\Controllers;

use App\Events\CreateParticipantInCyclingAppEvent;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ToolController extends Controller
{

    public function index(Request $request)
    {
        return view('tool.show')->with(['migratelink' => '', 'callping' => env("APP_URL") . '/api/ping', 'events' => [], 'transferurl' => env("APP_URL") . '/loppservice/api/transfer']);
    }

    public function run(Request $request)
    {
        Artisan::call('migrate', ["--force" => true]);
        Artisan::call('app:country-update');
        return view('tool.show')->with(['migratelink' => '', 'callping' => env("APP_URL") . '/api/ping', 'events' => Event::all(), 'transferurl' => env("APP_URL") . '/api/transfer']);
    }

    public function testappintegration(Request $request)
    {
        $response = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
        ])->get(env("BREVET_APP_URL") . '/ping');
        return $response;
    }


    public function publishToCyclingappIfNotAlreadyRegister(Request $request)
    {



        $course_uid = $request['event'];
        $reguid = $request['reguid'];

        if($reguid){
            $results = DB::table('registrations as r')
            ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
            ->distinct()
            ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
            ->join('contactinformation as ci', 'ci.person_person_uid', '=', 'p.person_uid')->where('r.registration_uid', '=', $reguid)->get();

        } else {

            $results = DB::table('registrations as r')
            ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
            ->distinct()
            ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
            ->join('contactinformation as ci', 'ci.person_person_uid', '=', 'p.person_uid')
            ->join('clubs as c', 'c.club_uid', '=', 'r.club_uid')
            ->join('adress as a', 'a.person_person_uid', '=', 'p.person_uid')
            ->join('countries as co', 'co.country_id', '=', 'a.country_id')
            ->where('r.course_uid', '=', $course_uid)
            ->whereNotIn('r.registration_uid', function ($query) {
                $query->select('registration_uid')
                    ->from('published_events');
            })
            ->get();

        }





        $count = 0;
        if(!$results->isEmpty()){
            foreach ($results as $result) {
                event(new CreateParticipantInCyclingAppEvent($result->course_uid, $result->person_uid, $result->registration_uid));
                sleep(1);
            }
            $count = count($results);
        } else {
            $count = 0;
        }
        return view('tool.show')->with(['migratelink' => '', 'callping' => env("APP_URL") . '/api/ping', 'events' => Event::all(), 'transferurl' => env("APP_URL") . '/api/transfer', 'counttransferred' => $count]);


    }

}