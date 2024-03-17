<?php

namespace App\Listeners;

use App\Events\CreateParticipantInCyclingAppEvent;
use App\Models\Event;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CreateParticipantInCyclingAppEventListener
{

    public function __construct()
    {
        //
    }

    public function handle(CreateParticipantInCyclingAppEvent $event): void
    {
        $person = Person::find($event->person_uid);
        $registration = Registration::find($event->registration_uid);
        $event_event = Event::find($registration->course_uid)->get()->first();
        $club = DB::table('clubs')->select('name')->where('club_uid', $registration->club_uid)->get()->first();

        $response = Http::post('http://localhost:8090' . '/participant/addparticipant/track/' . $registration->course_uid, [
            'participant' => $person,
            'registration' => $registration,
            'event_uid' => $event_event->event_uid,
            'club' => $club
        ]);
        if ($response->successful()) {
            $responseData = $response->json();
        } else {
            $errorCode = $response->status();
            $errorMessage = $response->body();
            Log::debug("Error transfering participant in app " . $registration->registration_uid . ' ' . ' With error ' . $errorCode);
            // Request failed, handle error



        }

        dd($response);

    }

}