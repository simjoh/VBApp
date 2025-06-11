<?php

namespace App\Listeners;

use App\Events\UpdateCompetitorInfoEvent;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\PublishedEvents;
use App\Models\ErrorEvents;
use Ramsey\Uuid\Uuid;

class UpdateCompetitorInfoEventListener
{
    public function __construct()
    {
    }

    public function handle(UpdateCompetitorInfoEvent $event): void
    {
        Log::debug("Handling update competitor info for registration_uid: " . $event->registration_uid);

        try {
            $person = Person::where('person_uid', $event->person_uid)->first();
            $registration = Registration::where('registration_uid', $event->registration_uid)->first();

            if (!$person || !$registration) {
                Log::error("Person or registration not found for registration_uid: " . $event->registration_uid);
                return;
            }

            // Prepare competitor info data
            $competitorInfoData = [
                'email' => $person->contactinformation->email ?? '',
                'phone' => $person->contactinformation->tel ?? '',
                'adress' => $person->adress->adress ?? '',
                'postal_code' => $person->adress->postal_code ?? '',
                'place' => $person->adress->city ?? '',
                'country' => $person->adress->country->country_name_sv ?? '',
                'country_id' => $person->adress->country_id ?? null
            ];

            // In the cycling app, competitor_uid is the same as person_uid
            $competitor_uid = $event->person_uid;

            Log::debug("Updating competitor info for competitor_uid: " . $competitor_uid, $competitorInfoData);

            $response = Http::withHeaders([
                'APIKEY' => env('BREVET_APP_API_KEY'),
                'Content-Type' => 'application/json',
                'User-Agent' => env('LOPPSERVICE_USER_AGENT')
            ])->put(env("BREVET_APP_URL") . '/competitor/' . $competitor_uid . '/info', $competitorInfoData);

            if ($response->successful()) {
                $responseData = json_decode($response->getBody(), true);

                Log::debug("Successfully updated competitor info for competitor_uid: " . $competitor_uid);
            } else {
                $errorCode = $response->status();
                $errorMessage = $response->body();


                Log::error("Error updating competitor info for competitor_uid: " . $competitor_uid .
                          " Status: " . $errorCode . " Message: " . $errorMessage);
            }
        } catch (Exception $e) {
            Log::error("Exception in UpdateCompetitorInfoEventListener", [
                'registration_uid' => $event->registration_uid,
                'person_uid' => $event->person_uid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
