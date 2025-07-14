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
        $club = DB::table('clubs')->select('name', 'club_uid')->where('club_uid', $registration->club_uid)->get()->first();


        $medal = DB::table('optionals')->where('registration_uid', $registration->registration_uid)->where('productID', 1014)->exists();


        $responseUid = Uuid::uuid4();

        // Get related data for participant
        $adress = DB::table('adress')->where('person_person_uid', $person->person_uid)->first();
        $contactinfo = DB::table('contactinformation')->where('person_person_uid', $person->person_uid)->first();

        // Create clean, simple arrays without Eloquent relationships, matching API expectations
        $participantArray = [
            'person_uid' => $person->person_uid,
            'firstname' => $person->firstname,
            'surname' => $person->surname,
            'birthdate' => $person->birthdate,
            'registration_registration_uid' => $person->registration_registration_uid,
            'created_at' => $person->created_at,
            'updated_at' => $person->updated_at,
            'checksum' => $person->checksum,
            'gender' => $person->gender,
            'gdpr_approved' => $person->gdpr_approved,
            'contactinformation' => $contactinfo ? [
                'contactinformation_uid' => $contactinfo->contactinformation_uid,
                'tel' => $contactinfo->tel,
                'email' => $contactinfo->email,
                'person_person_uid' => $contactinfo->person_person_uid,
                'created_at' => $contactinfo->created_at,
                'updated_at' => $contactinfo->updated_at
            ] : null,
            'adress' => $adress ? [
                'adress_uid' => $adress->adress_uid,
                'adress' => $adress->adress,
                'person_person_uid' => $adress->person_person_uid,
                'postal_code' => $adress->postal_code,
                'city' => $adress->city,
                'country_id' => $adress->country_id,
                'created_at' => $adress->created_at,
                'updated_at' => $adress->updated_at
            ] : null
        ];

        $registrationArray = [
            'registration_uid' => $registration->registration_uid,
            'course_uid' => $registration->course_uid,
            'additional_information' => $registration->additional_information,
            'use_physical_brevet_card' => $registration->use_physical_brevet_card,
            'reservation' => $registration->reservation,
            'reservation_valid_until' => $registration->reservation_valid_until,
            'startnumber' => $registration->startnumber,
            'club_uid' => $registration->club_uid,
            'created_at' => $registration->created_at,
            'updated_at' => $registration->updated_at,
            'ref_nr' => $registration->ref_nr,
            'person_uid' => $registration->person_uid
        ];

        // Fix club structure - API expects object with both club_uid and name
        $clubData = $club ? [
            'club_uid' => $club->club_uid,
            'name' => $club->name
        ] : null;

        $payload = [
            'participant' => $participantArray,
            'registration' => $registrationArray,
            'event_uid' => $event_event->event_uid,
            'club' => $clubData,
            'response_uid' => $responseUid->toString(),
            'medal' => $medal
        ];

        // Log the complete payload for debugging/copying
        Log::info("ADDPARTICIPANT PAYLOAD:", [
            'url' => env("BREVET_APP_URL") . '/participant/addparticipant/track/' . $registration->course_uid,
            'headers' => [
                'APIKEY' => env('BREVET_APP_API_KEY'),
                'User-Agent' => env('LOPPSERVICE_USER_AGENT')
            ],
            'payload' => $payload
        ]);

        $response = Http::withHeaders([
            'APIKEY' => env('BREVET_APP_API_KEY'),
            'User-Agent' => env('LOPPSERVICE_USER_AGENT')
        ])->post(env("BREVET_APP_URL") . '/participant/addparticipant/track/' . $registration->course_uid, $payload);

        if ($response->successful()) {
            $responseData = json_decode($response->getBody(), true);
            Log::debug($responseData);

            // Check if already published to avoid duplicate entries
            $existingPublished = PublishedEvents::where('registration_uid', $event->registration_uid)
                                                ->where('type', 'eventregistration')
                                                ->first();

            if (!$existingPublished) {
                $published = new PublishedEvents();
                $published->publishedevent_uid = Uuid::uuid4();
                $published->registration_uid = $event->registration_uid;
                $published->type = "eventregistration";
                $published->save();
                Log::debug("Transferring of participant detail was succesfylly transferred to cycling app " . "registration_uid:" .  $event->registration_uid);
            } else {
                Log::debug("Participant already published for registration_uid:" .  $event->registration_uid);
            }
        } else {
            $errorCode = $response->status();
            $rawErrorMessage = $response->getBody();

            // Log detailed error response
            Log::error("ADDPARTICIPANT API ERROR:", [
                'registration_uid' => (string) $event->registration_uid,
                'status_code' => $errorCode,
                'response_headers' => $response->headers(),
                'response_body_raw' => $rawErrorMessage,
                'url' => env("BREVET_APP_URL") . '/participant/addparticipant/track/' . $registration->course_uid,
                'request_payload' => $payload
            ]);

            // Try to decode JSON response for additional details
            $errorMessage = json_decode($rawErrorMessage, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Ensure UUIDs are properly stringified in the log
                if (isset($errorMessage['response_uid'])) {
                    $errorMessage['response_uid'] = (string) $errorMessage['response_uid'];
                }
                if (isset($errorMessage['registration_uid'])) {
                    $errorMessage['registration_uid'] = (string) $errorMessage['registration_uid'];
                }
                Log::error("ADDPARTICIPANT DECODED ERROR:", $errorMessage);
            }

            // Handle error event creation
            $responseUidForError = $responseUid->toString(); // Use the response_uid we sent
            if (is_array($errorMessage) && isset($errorMessage['response_uid'])) {
                $responseUidForError = $errorMessage['response_uid']; // Use response_uid from error if available
            }

            // Check if error event already exists for this registration
            $existingError = ErrorEvents::where('registration_uid', $event->registration_uid)
                                       ->where('type', 'eventregistration')
                                       ->first();

            if (!$existingError) {
                // Create new error event
                $error = new ErrorEvents();
                $error->errorevent_uid = Uuid::uuid4()->toString();
                $error->publishedevent_uid = $responseUidForError;
                $error->registration_uid = $event->registration_uid;
                $error->type = "eventregistration";
                $error->error_code = $errorCode;
                $error->error_message = is_array($errorMessage) && isset($errorMessage['message'])
                    ? $errorMessage['message']
                    : $rawErrorMessage;
                $error->save();

                // Convert UUIDs to strings for logging
                $errorEventUid = $error->errorevent_uid;
                if (is_object($errorEventUid) && method_exists($errorEventUid, 'toString')) {
                    $errorEventUid = $errorEventUid->toString();
                }

                Log::info("Created error event for failed participant transfer", [
                    'registration_uid' => (string) $event->registration_uid,
                    'errorevent_uid' => $errorEventUid,
                    'error_code' => $errorCode
                ]);

                // Fire the failed transfer event
                event(new FailedParticipantTransferEvent($event->registration_uid, $error->errorevent_uid));
            } else {
                Log::info("Error event already exists for registration_uid: " . $event->registration_uid);

                // Fire the failed transfer event with existing error
                event(new FailedParticipantTransferEvent($event->registration_uid, $existingError->errorevent_uid));
            }
        }
    }
}
