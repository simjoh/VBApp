<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Models\Adress;
use App\Models\Club;
use App\Models\Contactinformation;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Person;
use App\Models\Product;
use App\Models\Registration;
use App\Models\StartNumberConfig;
use App\Rules\EmailEquals;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{


    public function complete(Request $request): RedirectResponse
    {
        $registration_uid = $request['regsitrationUid'];
        $preregistration = Registration::where('registration_uid', $registration_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();
        $preregistration->reservation = false;
        $preregistration->reservation_valid_until = null;
        // Skapa ett lösen
        return to_route('checkout', ["reg" => $registration_uid]);
    }


    public function reserve(Request $request): RedirectResponse
    {
        return to_route('checkout');
    }

    public function update(Request $request)
    {
        $registration = Registration::find($request['registration_uid'])->get()->first();
        $registration->person->firstname = $request['first_name'];
        $registration->person->surname = $request['last_name'];
        $registration->person->adress->city = $request['city'];
        $registration->person->adress->adress = $request['street-address'];
        $registration->person->adress->postal_code = $request['postal-code'];
        $registration->additional_information = $request['extra-info'];
        $registration->contactinformation->tel = $request['tel'];
        $registration->contactinformation->email = $request['email'];
        $registration->person()->save($registration->person);
        $registration->save();
        return view('registrations.success')->with(['text' => 'Your registration is updated']);
    }

    public function existingregistration(Request $request)
    {
        return view('registrations.edit')->with(['countries' => \App\Models\Country::all()->sortByDesc("country_name_en"), 'registration' => Registration::find($request['regsitrationUid'])->with(['person'])->get()->first()]);
    }


    public function create(Request $request): RedirectResponse
    {
        // kolla att det finns ett event med det uidet
        $event = Event::find($request['uid'])->first();

        if (!$event) {
            http_response_code(404);
            exit();
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'email-confirm' => 'required|regex:/(.+)@(.+)\.(.+)/i'
        ]);

        // Skapa en registrering
        $reg_uid = Uuid::uuid4();
        $registration = new Registration();
        $registration->registration_uid = $reg_uid;
        // banans uid hårdkoda tills vi bygg ut möjlighet att överföra från brevet applikationen
        $registration->course_uid = 'd32650ff-15f8-4df1-9845-d3dc252a7a84';
        $registration->additional_information = $request['extra-info'];
        // sätt reserve eller complete baserat på
        if ($request->input('save') === 'reserve') {
            $registration->reservation = true;
            $registration->reservation_valid_until = '2023-12-31';
        } else {
            $registration->reservation = false;
        }

        $registration->startnumber = $this->getStartnumber('d32650ff-15f8-4df1-9845-d3dc252a7a84', $event->eventconfiguration->startnumberconfig);

       // $club = Club::where('name', $request['club']);

        // Kolla om vi sparat klubben sen tidigare
        $club =   Club::whereRaw('LOWER(`name`) LIKE ? ',[trim(strtolower($request['club'])).'%'])->first();

        if(!$club){
            $club_uid =  Uuid::uuid4();
            $club = new Club();
            $club->club_uid = $club_uid;
            $club->name = $request['club'];
            $club->description = null;
            $club->official_club = false;
            $club->save();
            $registration->club_uid = $club_uid;
        } else {
            $registration->club_uid = $club->club_uid;
        }


        $registration->save();

        $reg = Registration::find($reg_uid);

        $person = new Person();
        $person->person_uid = Uuid::uuid4();
        $person->firstname = $request['first_name'];
        $person->surname = $request['last_name'];
        $person->birthdate = $request['year']. "-" . $request['month'] . "-" . $request['day'];
        $person->registration_registration_uid = $reg->registration_uid;


        $adress = new Adress();
        $adress->adress_uid = Uuid::uuid4();
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->country_id = $request['country'];
        $adress->city = $request['city'];
        $adress->person_person_uid = $person->person_uid;

        $contact = new Contactinformation();
        $contact->contactinformation_uid = Uuid::uuid4();
        $contact->tel = $request['tel'];
        $contact->email = $request['email'];

        $country = Country::find($request['country']);
        $registration->person()->save($person);
        $person->adress()->save($adress);
        $person->contactinformation()->save($contact);
        $person->adress()->country = $country->country_id;


        //ta hand om  extra tillvalen. väldigt oflexibelt just nu men funkar för det mest initiala

        $productIds = Product::all('productID')->toArray();;

        foreach ($productIds as $product) {
            if ($request[$product['productID']] == 'on') {
                $optional = new Optional();
                $optional->registration_uid = $reg->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }

            if (strval($request['productID']) == strval($product['productID'])) {
                $optional = new Optional();
                $optional->registration_uid = $reg->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }

            if (strval($request['jersey']) == strval($product['productID'])) {
                $optional = new Optional();
                $optional->registration_uid = $reg->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }
        }

//        $optionals = Optional::where('registration_uid',$reg_uid)->get();
//
//        event(new CompletedRegistrationSuccessEvent($reg,$optionals));
        return to_route('checkout', ["reg" => $reg->registration_uid]);
    }

    private function getStartnumber(string $course_uid, StartNumberConfig $startNumberConfig): int
    {

        $max = Registration::where('course_uid', $course_uid)->max('startnumber');
        if ($max == null) {
            return $startNumberConfig->begins_at;
        } else {
            return $max + $startNumberConfig->increments;
        }
    }
}
