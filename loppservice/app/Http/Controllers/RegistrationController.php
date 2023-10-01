<?php

namespace App\Http\Controllers;

use App\Enums\Months;
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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{


    public function complete(Request $request): RedirectResponse
    {
        $registration_uid = $request['regsitrationUid'];
        $preregistration = Registration::where('registration_uid', $registration_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();
        $preregistration->reservation = false;
        $preregistration->reservation_valid_until = null;
        $preregistration->save();
        return to_route('checkout', ["reg" => $registration_uid, 'completeregistration' => true]);
    }


    public function reserve(Request $request): RedirectResponse
    {
        return to_route('checkout');
    }

    public function update(Request $request)
    {
        $registration = Registration::where('registration_uid', $request['registration_uid'])->get()->first();
        $registration->person->firstname = $request['first_name'];
        $registration->person->surname = $request['last_name'];
        $registration->additional_information = $request['extra-info'];
        $registration->person->contactinformation->tel = $request['tel'];
        $registration->person->contactinformation->email = $request['email'];
        $registration->person->birthdate = $request['year'] . "-" . $request['month'] . "-" . $request['day'];

        $person = $registration->person;
        $adress = $person->adress;
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->city = $request['city'];
        $person->adress = $adress;
        $person->adress->save();

        $contact = $person->contactinformation;
        $contact->tel = $request['tel'];
        $email = $contact->email;
        $contact->email = $email;
        $person->contactinformation->update();

        $club = Club::whereRaw('LOWER(`name`) LIKE ? ', [trim(strtolower($request['club'])) . '%'])->first();

        if (!$club) {
            $club_uid = Uuid::uuid4();
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

        //$optionals = Optional::where('registration_uid', $registration->registration_uid);

        $registration->save();
        return view('registrations.success')->with(['text' => 'Your registration is updated']);
    }

    public function existingregistration(Request $request)
    {

        $registration = DB::table('registrations')
            ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
            ->join('person', 'person.registration_registration_uid', '=', 'registrations.registration_uid')
            ->join('adress', 'adress.person_person_uid', '=', 'person.person_uid')
            ->join('contactinformation', 'contactinformation.person_person_uid', '=', 'person.person_uid')
            ->join('countries', 'countries.country_id', '=', 'adress.country_id')
            ->select('registrations.*', 'person.*', 'adress.*', 'countries.*', 'contactinformation.*', 'clubs.name AS club_name')
            ->where('registrations.registration_uid', $request['registrationUid'])
            ->get()->first();

        if ($registration) {
            $birthdate = explode("-", $registration->birthdate);
            $day = $birthdate[2];
            $month = $birthdate[1];
            $year = $birthdate[0];
        }
        $months = array();
        foreach (Months::cases() as $shape) {
            $temparr = array(strval(Months::getLabel($shape)['ord']) => Months::getLabel(Months::tryFrom($shape->value))['en']);
            $months = array_merge($months, $temparr);
        }

        return view('registrations.edit')->with(['countries' => Country::all()->sortByDesc("country_name_en"), 'years' => range(date('Y'), 1950), 'days' => range(date('d'), 31), 'months' => $months, 'registration' => $registration, 'day' => $day, 'month' => $month, 'birthyear' => $year]);
    }


    public function create(Request $request): RedirectResponse
    {
        // kolla att det finns ett event med det uidet
        $event = Event::find($request['uid'])->first();

        if (!$event) {
            http_response_code(404);
            exit();
        }

//        $date_now = date("Y-m-d"); // this format is string comparable
//
//        if ($date_now != date('Y-m-d','2023-10-01')) {
//            http_response_code(404);
//        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'email-confirm' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'year' => 'required',
            'month' => 'required',
            'day' => 'required'
        ]);

        if ($this->isExistingregistrationEmailOnCourse($request['email'])) {
            return back()->withErrors(['same' => 'Registration with this email already exists' . " " . $request['email'] . ". Please use another emailadress"])->withInput();
        }


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

        // Kolla om vi sparat klubben sen tidigare
        $club = Club::whereRaw('LOWER(`name`) LIKE ? ', [trim(strtolower($request['club'])) . '%'])->first();

        if (!$club) {
            $club_uid = Uuid::uuid4();
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
        $person->birthdate = $request['year'] . "-" . $request['month'] . "-" . $request['day'];
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
        //$optionals = Optional::where('registration_uid', $reg_uid)->get();
        // event(new PreRegistrationSuccessEvent($reg));
        // event(new CompletedRegistrationSuccessEvent($registration));
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

    private function isExistingregistrationEmailOnCourse(string $email): bool
    {
        $contact = Contactinformation::where('email', $email)->get()->first();
        if ($contact) {
            $regs = Registration::where('course_uid', 'd32650ff-15f8-4df1-9845-d3dc252a7a84')->get();
            foreach ($regs as $reg) {
                if (strtolower($reg->person->contactinformation->email) == strtolower($email)) {
                    break;
                }
            }
            return true;
        }
        return false;
    }
}
