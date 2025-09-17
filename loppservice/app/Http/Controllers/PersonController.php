<?php

namespace App\Http\Controllers;

use App\Models\Adress;
use App\Models\Contactinformation;
use App\Models\Optional;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    /**
     * Get person information including address and contact information
     */
    public function get(Request $request)
    {
        $personUid = $request->input('person_uid');

        if (!$personUid) {
            return response()->json(['error' => 'Person UID is required'], 400);
        }

        try {
            $person = Person::where('person_uid', $personUid)
                ->with(['adress', 'contactinformation'])
                ->first();

            if (!$person) {
                return response()->json(['error' => 'Person not found'], 404);
            }

            $personData = [
                'person_uid' => $person->person_uid,
                'firstname' => $person->firstname,
                'surname' => $person->surname,
                'birthdate' => $person->birthdate,
                'gender' => $person->gender,
                'address' => $person->adress ? [
                    'street' => $person->adress->street,
                    'postal_code' => $person->adress->postal_code,
                    'city' => $person->adress->city,
                    'country' => $person->adress->country,
                ] : null,
                'contact_information' => $person->contactinformation ? [
                    'email' => $person->contactinformation->email,
                    'phone' => $person->contactinformation->phone,
                ] : null,
                'created_at' => $person->created_at,
                'updated_at' => $person->updated_at,
            ];

            return response()->json($personData, 200);

        } catch (\Exception $e) {
            Log::error('Error getting person data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get person data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Forget all information about a person including registrations, orders, optionals, and personal data
     */
    public function forget(Request $request)
    {
        $personUid = $request->input('person_uid');

        if (!$personUid) {
            return response()->json(['error' => 'Person UID is required'], 400);
        }

        try {
            DB::transaction(function () use ($personUid) {
                // Find the person
                $person = Person::where('person_uid', $personUid)->first();

                if (!$person) {
                    throw new \Exception('Person not found');
                }

                // Get all registrations for this person
                $registrations = Registration::where('person_uid', $personUid)->get();

                // Delete all related data for each registration
                foreach ($registrations as $registration) {
                    // Delete related optionals
                    Optional::where('registration_uid', $registration->registration_uid)->delete();

                    // Delete related orders
                    DB::table('orders')->where('registration_uid', $registration->registration_uid)->delete();

                    // Delete the registration
                    $registration->delete();
                }

                // Delete contact information
                Contactinformation::where('person_person_uid', $personUid)->delete();

                // Delete address
                Adress::where('person_person_uid', $personUid)->delete();

                // Delete the person
                $person->delete();
            });

            Log::info('Person data forgotten successfully: ' . $personUid);
            return response()->json(['message' => 'All person data has been forgotten successfully'], 200);

        } catch (\Exception $e) {
            Log::error('Error forgetting person data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to forget person data: ' . $e->getMessage()], 500);
        }
    }
}
