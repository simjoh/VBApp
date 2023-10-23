<?php

namespace App\Http\Controllers;

use App\Models\Adress;
use App\Models\Club;
use App\Models\Contactinformation;
use App\Models\Country;
use App\Models\Event;
use App\Models\Optional;
use App\Models\Person;
use App\Models\Product;
use App\Models\Registration;
use App\Traits\DaysTrait;
use App\Traits\MonthsTrait;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{
    use MonthsTrait;
    use DaysTrait;
    public function index(Request $request)
    {
        if (!Str::isUuid($request['uid'])) {
            return view('registrations.updatesuccess')->with(['text' => __('Invalid request')]);
        }
        $event = Event::find($request['uid'])->first();

        if (!$event) {
            return view('registrations.updatesuccess')->with(['text' => __('Yo try to acesss registration form for non existing event')]);
        }

        $count = Registration::where('course_uid', $request['uid'])->count();
        if ($count >= $event->eventconfiguration->max_registrations) {
            return view('registrations.updatesuccess')->with(['text' => __('Event has reached the maximum number of registered participants')]);
        }

        if ($event->eventconfiguration->reservationconfig->use_reservation_on_event && Carbon::now()->endOfDay()->lte(Carbon::parse($event->eventconfiguration->reservationconfig->use_reservation_until)->endOfDay()) == true) {
            $reservationactive = true;
        } else {
            $reservationactive = false;
        }

        return view('registrations.show')->with(['showreservationbutton' => $reservationactive,
            'countries' => Country::all()->sortByDesc("country_name_en"),
            'years' => range(date('Y'), 1950)]);
    }


    public function complete(Request $request): RedirectResponse
    {
        $registration_uid = $request['regsitrationUid'];
        $preregistration = Registration::where('registration_uid', $registration_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();
        $preregistration->save();
        return to_route('checkout', ["reg" => $registration_uid, 'is_final_registration_on_event' => true]);
    }


    public function reserve(Request $request): RedirectResponse
    {
        return to_route('checkout');
    }

    public function update(Request $request)
    {
        $registration = Registration::where('registration_uid', $request['registration_uid'])->get()->first();

        if (!$registration) {
            http_response_code(404);
            exit();
        }

        $registration->person->firstname = $request['first_name'];
        $registration->person->surname = $request['last_name'];
        $registration->additional_information = $request['extra-info'];
        $registration->person->contactinformation->tel = $request['tel'];
        $registration->person->birthdate = $request['year'] . "-" . str_pad($request['month'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($request['day'], 2, "0", STR_PAD_LEFT);

        $person = $registration->person;
        $adress = $person->adress;
        $adress->adress = $request['street-address'];
        $adress->postal_code = $request['postal-code'];
        $adress->city = $request['city'];
        $person->adress->update();

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
        $registration->person->update();
        $registration->save();

        $productIds = Product::all('productID')->toArray();
        $opt = Optional::where('registration_uid', $registration->registration_uid)->get();
        foreach ($opt as $o) {
            $o->delete();
        }
        // gör mer dynamiskt
        $productIds = Product::all('productID')->toArray();;

        foreach ($productIds as $product) {
            if ($request[$product['productID']] == 'on') {
                $optional = new Optional();
                $optional->registration_uid = $registration->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }

            if (strval($request['productID']) == strval($product['productID'])) {
                $optional = new Optional();
                $optional->registration_uid = $registration->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }
        }

        return view('registrations.updatesuccess')->with(['text' => 'Your registration details is updated']);
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

        if (!$registration) {
            http_response_code(404);
            exit();
        }

        if ($registration) {
            $birthdate = explode("-", $registration->birthdate);
            $day = $birthdate[2];
            $month = $birthdate[1];
            $year = $birthdate[0];
        }

        $optionalsforreg = Optional::where('registration_uid', $registration->registration_uid)->pluck('productID');

        return view('registrations.edit')->with(['countries' => Country::all()->sortByDesc("country_name_en"), 'years' =>
            range(date('Y'), 1950), 'days' => $this->daysforSelect(), 'months' => $this->monthsforSelect(),
            'registration' => $registration, 'day' => $day, 'month' => $month,
            'birthyear' => $year, 'optionalsforregistration' => $optionalsforreg]);
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
            'email-confirm' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'year' => 'required',
            'month' => 'required',
            'day' => 'required'
        ]);

//        if ($this->isExistingregistrationWithTelOnCourse($request['tel'])) {
//            return back()->withErrors(['same' => 'Registration with this email already exists' . " " . $request['tel'] . ". Please use another phonenumber"])->withInput();
//        }


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
        $person->firstname = Str::of($request['first_name'])->ucfirst();
        $person->surname = Str::of($request['last_name'])->ucfirst();
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
        //  event(new PreRegistrationSuccessEvent($reg));
        // event(new CompletedRegistrationSuccessEvent($registration));
        // event(new CanceledPaymentEvent($reg->registration_uid, false));
        // event(new FailedPaymentEvent($registration->registration_uid));
        return to_route('checkout', ["reg" => $reg->registration_uid]);
    }

    private function isExistingregistrationWithTelOnCourse(string $tel): bool
    {
        $contact = Contactinformation::where('tel', $tel)->get()->first();
        if ($contact) {
            $regs = Registration::where('course_uid', 'd32650ff-15f8-4df1-9845-d3dc252a7a84')->get();
            foreach ($regs as $reg) {
                if (strtolower($reg->person->contactinformation->email) == strtolower($tel)) {
                    break;
                }
            }
            return true;
        }
        return false;
    }
}
