<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\PreRegistrationSuccessEvent;
use App\Models\Adress;
use App\Models\Contactinformation;
use App\Models\Country;
use App\Models\Person;
use App\Models\Registration;
use Exception;
use GuzzleHttp\Psr7\Response;
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

    public function update(Request $request)
    {
       $registration =  Registration::find($request['registration_uid'])->get()->first();
       $registration->person->firstname = $request['first_name'];
       $registration->person->surname = $request['last_name'];
       $registration->person->adress->city = $request['city'];
       $registration->person->adress->adress = $request['street-address'];
       $registration->person->adress->postal_code = $request['postal-code'];
       $registration->additional_information = $request['extra-info'];
       $registration->person()->save($registration->person);
        $registration->save();
        return view('registrations.success')->with(['text' => 'Your registration is updated']);
    }

    public function existingregistration(Request $request)
    {
        return view('registrations.edit')->with(['countries' => \App\Models\Country::all()->sortByDesc("country_name_en"), 'registration' => Registration::find($request['regsitrationUid'])->with(['person'])->get()->first()]);
    }



    /**
     * Show the form to create a new blog post.
     */
    public function create(Request $request): RedirectResponse
    {
        $count = Registration::count();

        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ]);


        if($request->input('save') === 'reserve'){
            $this->makereservation($request);
           return to_route('checkout');
        }

        $reg_uid = Uuid::uuid4();

        $registration = new Registration();
        $registration->registration_uid = $reg_uid;
        // banans uid hårdkoda tills vi bygg ut möjlighet att överföra från brevet applikationen
        $registration->course_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $registration->additional_information = $request['extra-info'];
        $registration->reservation = false;
        $registration->save();

        $reg = Registration::find($reg_uid);

        $person = new Person();
        $person->person_uid = Uuid::uuid4();
        $person->firstname = $request['first_name'];
        $person->surname = $request['last_name'];
        $person->birthdate = '2022-03-02';
        $person->registration_registration_uid = $reg->registration_uid;


        $adress = new Adress();
        $adress->adress_uid = Uuid::uuid4();
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->country_id = $request['country'];
        $adress->city = $request['city'];
        $adress->person_person_uid = $person->person_uid;

//        $this->makeAdress($request, $person,Country::find($request['country']));
//        $this->makeContactinformation($request);


        $contact = new Contactinformation();
        $contact->contactinformation_uid  = Uuid::uuid4();
        $contact->tel = '12345';
        $contact->email = $request['email'];


        $country = Country::find($request['country']);
        $registration->person()->save($person);
        $person->adress()->save($adress);
        $person->contactinformation()->save($contact);
        $person->adress()->country = $country->country_id;

        $user = Registration::find($reg_uid)->get()->first();

        event(new CompletedRegistrationSuccessEvent($user));

        return to_route('checkout');
    }

//    private function makeAdress(Request $request, Person $person, Country $country): Adress{
//        $adress = new Adress();
//        $adress->adress_uid = Uuid::uuid4();
//        $adress->adress = $request['street-address'];
//        $adress->postal_code = $request['postal-code'];
//        $adress->country_id = $request['country'];
//        $adress->city = $request['city'];
//        $adress->person_person_uid = $person->person_uid;
//        $adress->country = $country->country_id;
//        return $adress;
//    }
//
//    private function makeContactinformation(Request $request): Contactinformation{
//        $contact = new Contactinformation();
//        $contact->contactinformation_uid  = Uuid::uuid4();
//        $contact->tel = '12345';
//        $contact->email = $request['email'];
//        return $contact;
//    }

    private function makereservation(Request $request){

        $reg_uid = Uuid::uuid4();

        $registration = new Registration();
        $registration->registration_uid = $reg_uid;
        // banans uid hårdkoda tills vi bygg ut möjlighet att överföra från brevet applikationen
        $registration->course_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $registration->additional_information = $request['extra-info'];
        $registration->reservation = true;
        $registration->save();

        $reg = Registration::find($reg_uid);

        $person = new Person();
        $person->person_uid = Uuid::uuid4();
        $person->firstname = $request['first_name'];
        $person->surname = $request['last_name'];
        $person->birthdate = '2022-03-02';
        $person->registration_registration_uid = $reg->registration_uid;


        $adress = new Adress();
        $adress->adress_uid = Uuid::uuid4();
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->country_id = $request['country'];
        $adress->city = $request['city'];
        $adress->person_person_uid = $person->person_uid;

        $contact = new Contactinformation();
        $contact->contactinformation_uid  = Uuid::uuid4();
        $contact->tel = '12345';
        $contact->email = $request['email'];


        $country = Country::find($request['country']);
        $registration->person()->save($person);
        $person->adress()->save($adress);
        $person->contactinformation()->save($contact);
        $person->adress()->country = $country->country_id;

        $user = Registration::find($reg_uid)->get()->first();

        //dd($regtopublish->person->adress->adress);
        event(new PreRegistrationSuccessEvent($user));

    }
}
