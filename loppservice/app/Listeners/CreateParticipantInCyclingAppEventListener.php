<?php

namespace App\Listeners;

use App\Events\CreateParticipantInCyclingAppEvent;
use App\Events\FailedParticipantTransferEvent;
use App\Models\ErrorEvents;
use App\Models\Event;
use App\Models\Person;
use App\Models\PublishedEvents;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class CreateParticipantInCyclingAppEventListener
{
    public function __construct(){}
    public function handle(CreateParticipantInCyclingAppEvent $event): void
    {
        Log::debug("Handling creatparticiapant for registration_uid: " . $event->registration_uid);
        $person = Person::where('person_uid',$event->person_uid)->get()->first();
        $registration = Registration::where('registration_uid',$event->registration_uid)->get()->first();
        $event_event = Event::find($registration->course_uid)->get()->first();
        $club = DB::table('clubs')->select('name')->where('club_uid', $registration->club_uid)->get()->first();


        $medal = DB::table('optionals')->where('registration_uid', $registration->registration_uid)->where('productID', 1014)->exists();

/*         $responses = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
        ])->get(env("BREVET_APP_URL") . '/ping'); */

        $responseUid = Uuid::uuid4();

        $response = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
        ])->post(env("BREVET_APP_URL") . '/participant/addparticipant/track/' . $registration->course_uid, [
            'participant' => $person,
            'registration' => $registration,
            'event_uid' => $event_event->event_uid,
            'club' => $club,
            'response_uid' => $responseUid,
            'medal' => $medal
        ]);

        if ($response->successful()) {
            $responseData = json_decode($response->getBody(), true);
            Log::debug($responseData);
                $published = new PublishedEvents();
                $published->publishedevent_uid = Uuid::uuid4();
                $published->registration_uid = $event->registration_uid;
                $published->type = "eventregistration";
                $published->save();
            Log::debug("Transferring of participant detail was succesfylly transferred to cycling app " . "registration_uid:" .  $event->registration_uid);
        } else {
            $errorCode = $response->status();
            $errorMessage = json_decode($response->getBody(), true);
            if (!ErrorEvents::where('publishedevent_uid', $errorMessage['response_uid'])->exists()) {
                $error = new ErrorEvents();
                $error->errorevent_uid = Uuid::uuid4();
                $error->publishedevent_uid = Uuid::uuid4();
                $error->registration_uid = $event->registration_uid;
                $error->type = "eventregistration";
                $error->save();
                event(new FailedParticipantTransferEvent($event->registration_uid, $error->errorevent_uid));
            } else {
                $error =ErrorEvents::where('publishedevent_uid', $errorMessage['response_uid'])->get();
                event(new FailedParticipantTransferEvent($event->registration_uid, $error->errorevent_uid));
            }
            Log::debug("Error transfering participant in app with registration_uid: " . $event->registration_uid . ' ' . ' With error ' . $errorCode);
        }
    }
}