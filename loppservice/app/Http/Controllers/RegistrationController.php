<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\PreRegistrationSuccessEvent;
use App\Models\Adress;
use App\Models\Person;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{


    public function complete(Request $request): RedirectResponse
    {
        $registration_uid = $request['regsitrationUid'];
        $preregistration = Registration::where('registration_uid', $registration_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();
//        dd($preregistration);
        event(new CompletedRegistrationSuccessEvent($preregistration));
        return to_route('checkout');
    }


    public function reserve(Request $request): RedirectResponse
    {
        return to_route('checkout');
    }

    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required',
        ]);

        $count = Registration::count();

        $reg_uid = Uuid::uuid4();

        $registration = new Registration();
        $registration->registration_uid = $reg_uid;
        // banans uid hårdkoda tills vi bygg ut möjlighet att överföra från brevet applikationen
        $registration->course_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $registration->additional_information = "testsdsad";
        $registration->save();


        $reg = Registration::find($reg_uid);

        $person = new Person();
        $person->person_uid = Uuid::uuid4();
        $person->firstname = $request['first_name'];
        $person->surname = $request['last_name'];
        $person->birthdate = '2022-03-02';
        // $person->registration_uid = $reg->registration_uid;
        $person->registration_registration_uid = $reg->registration_uid;
        $person->save();

        $adress = new Adress();
        $adress->adress_uid = Uuid::uuid4();
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->country = $request['country'];
        $adress->city = $request['city'];
        $adress->person_person_uid = $person->person_uid;
        $adress->save();

        $regtopublish = Registration::with(['person.adress', 'person.contactinformation'])->find($reg_uid);

        $regtopublish = Registration::where('registration_uid', $reg_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();

        //dd($regtopublish->person->adress->adress);
        event(new PreRegistrationSuccessEvent($regtopublish));

        return to_route('checkout');
    }
}
