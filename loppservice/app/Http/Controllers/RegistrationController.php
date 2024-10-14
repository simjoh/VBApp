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
use App\Traits\DaysTrait;
use App\Traits\GenderTrait;
use App\Traits\HashTrait;
use App\Traits\MonthsTrait;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{
    use MonthsTrait;
    use DaysTrait;
    use HashTrait;
    use GenderTrait;


    public function index(Request $request)
    {

        $eventType = $request->query('event_type');


        if (!Str::isUuid($request['uid'])) {
            return view('registrations.updatesuccess')->with(['text' => __('Invalid request')]);
        }
        $event = Event::find($request['uid']);

        if (!$event) {
            return view('registrations.updatesuccess')->with(['text' => __('Yo try to acesss registration form for non existing event')]);
        }

        $count = Registration::where('course_uid', $request['uid'])->count();
        if ($count > $event->eventconfiguration->max_registrations) {
            return view('registrations.updatesuccess')->with(['text' => __('Event has reached the maximum number of registered participants')]);
        }
        $collection = collect($event->eventconfiguration->products);
        if ($event->eventconfiguration->reservationconfig->use_reservation_on_event && Carbon::now()->endOfDay()->lte(Carbon::parse($event->eventconfiguration->reservationconfig->use_reservation_until)->endOfDay()) == true) {
            $reservationactive = true;
            $resevation_product = $event->eventconfiguration->products->where('categoryID', 7)->first();
        } else {
            $reservationactive = false;
        }
        // registrera sig kan man alltid göra
        $registration_product = $event->eventconfiguration->products->where('categoryID', 6)->first();


        if ($eventType === 'BRM') {
            return view('registrations.brevet')->with(['showreservationbutton' => $reservationactive,
                'countries' => Country::all()->sortByDesc("country_name_en"), 'event' => $event->event_uid,
                'years' => range(date('Y', strtotime('-18 year')), 1950), 'registrationproduct' => $registration_product->productID, 'reservationproduct' => $reservationactive == false ? null : $resevation_product->productID, 'genders' => $this->gendersSv()]);
        }

        return view('registrations.show')->with(['showreservationbutton' => $reservationactive,
            'countries' => Country::all()->sortByDesc("country_name_en"),
            'years' => range(date('Y', strtotime('-18 year')), 1950), 'registrationproduct' => $registration_product->productID, 'reservationproduct' => $reservationactive == false ? null : $resevation_product->productID, 'genders' => $this->gendersEn()]);
    }


    public function complete(Request $request)
    {
        $registration_uid = $request['regsitrationUid'];

        $preregistration = Registration::where('registration_uid', $registration_uid)->with(['person.adress', 'person.contactinformation'])->get()->first();
        $current_event = Event::where('course_uid', $preregistration->course_uid);

        $nowDate = Carbon::now();
        $rerservationvlidto = Carbon::parse($current_event->eventconfiguration->reservationconfig->use_reservation_until);
        if ($nowDate->gt($rerservationvlidto)) {
            return view('registrations.updatesuccess')->with(['text' => 'Reservation link has expirered. Link was valid until end of ' . $rerservationvlidto]);
        }

        $preregistration->save();
        $reg_product = Product::find($request->query('productID'));

        if (App::isProduction()) {
            if (!$reg_product) {
                return to_route('checkout', ["reg" => $registration_uid, 'is_final_registration_on_event' => true, 'price_id' => 'price_1NvL2CLnAzN3QPcUka5kMIwR']);
            } else {
                return to_route('checkout', ["reg" => $registration_uid, 'is_final_registration_on_event' => true, 'price_id' => $reg_product->price_id]);
            }
        } else {
            return to_route('checkout', ["reg" => $registration_uid, 'is_final_registration_on_event' => true, 'price_id' => env("STRIPE_TEST_PRODUCT")]);
        }
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
        $string_to_hash = strtolower($request['first_name']) . strtolower($request['last_name']) . strtolower($request['year'] . "-" . $request['month'] . "-" . $request['day']);


        $person = Person::find($registration->person_uid);
        $person->checksum = $this->hashsumfor($string_to_hash);
        $person->firstname = $request['first_name'];
        $person->surname = $request['last_name'];
        $person->gender = $request['gender'];
        $person->contactinformation->tel = $request['tel'];
        $person->birthdate = $request['year'] . "-" . str_pad($request['month'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($request['day'], 2, "0", STR_PAD_LEFT);

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

        $registration->additional_information = $request['extra-info'];

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
        $person->update();
        $registration->update();

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

//        $result = DB::connection('vbapp')->select('SELECT * FROM competitors  WHERE competitor_uid = ?', ['2922a6e9-9e32-4832-9575-b3d2eb3011b9']);
//        if (count($result) > 0) {
//            $data = [
//                'given_name' => $person->firstname,
//                'family_name' => $person->surname,
//                'birthdate' => $person->birthdate,
//            ];
//            $affectedRows = DB::connection('vbapp')->table('competitors')->where('competitor_uid', '2922a6e9-9e32-4832-9575-b3d2eb3011b9')->update($data);
//            if ($affectedRows > 0) {
//
//                $competitor_info_data = [
//                    'email' => $adress->email,
//                    'adress' => $adress->adress,
//                    'postal_code' => $adress->postal_code,
//                    'place' => $adress->city,
//                    'cuntry_id' => $adress->country_id,
//                ];
//
//                $info = DB::connection('vbapp')->select('SELECT * FROM competitor_info  WHERE competitor_uid = ?', ['2922a6e9-9e32-4832-9575-b3d2eb3011b9']);
//                if (count($info) > 0) {
//                    $affectedRows = DB::connection('vbapp')->table('competitor_info')->where('competitor_uid', '2922a6e9-9e32-4832-9575-b3d2eb3011b9')->update($competitor_info_data);
//                } else {
//                    $competitor_info_data['competitor_uid'] = '2922a6e9-9e32-4832-9575-b3d2eb3011b9';
//                    $affectedRows = DB::connection('vbapp')->table('competitor_info')->insert($competitor_info_data);
//                }
//
//                echo "Record updated successfully.";
//            } else {
//                echo "No record found to update.";
//            }
//        }


        return view('registrations.updatesuccess')->with(['text' => 'Your registration details is updated']);
    }

    public function existingregistration(Request $request)
    {

        $registration = DB::table('registrations')
            ->join('clubs', 'clubs.club_uid', '=', 'registrations.club_uid')
            ->join('person', 'person.person_uid', '=', 'registrations.person_uid')
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
            'birthyear' => $year, 'optionalsforregistration' => $optionalsforreg, 'genders' => $this->gendersEn()]);
    }


    public function create(Request $request): RedirectResponse
    {

        $event = Event::where('event_uid', $request['uid'])->get()->first();

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

        $string_to_hash = strtolower($request['first_name']) . strtolower($request['last_name']) . strtolower($request['year'] . "-" . $request['month'] . "-" . $request['day']);
        // Rimligen är en och samma person bara registrerad en gång per event
        if (Person::where('checksum', $this->hashsumfor($string_to_hash))->exists()) {
            $person = Person::where('checksum', $this->hashsumfor($string_to_hash))->first();
            $person->gender = $request['gender'];

            $registrationsforperson = $person->registration;
            if ($registrationsforperson) {
                foreach ($registrationsforperson as $x) {
                    if ($x->course_uid === $request['uid']) {
                        return back()->withErrors(['same' => 'You already registered on event'])->withInput();
                    }
                }
            }
            $adress = $person->adress;
            $adress->adress = $request['street-address'];
            $adress->postal_code = $request['postal-code'];
            $adress->country_id = $request['country'];
            $adress->city = $request['city'];
            $adress->person_person_uid = $person->person_uid;

            $person->adress->save();

            $contact = $person->contactinformation;
            $contact->contactinformation_uid = Uuid::uuid4();
            $contact->tel = $request['tel'];
            $contact->email = $request['email'];
            $person->contactinformation->save();

        } else {
            $person = new Person();
            $person->gender = $request['gender'];
            $person->person_uid = Uuid::uuid4();
            $person->firstname = Str::of($request['first_name'])->ucfirst();
            $person->surname = Str::of($request['last_name'])->ucfirst();
            $person->birthdate = $request['year'] . "-" . $request['month'] . "-" . $request['day'];
            $person->registration_registration_uid = '1111111';
            $person->checksum = $this->hashsumfor($string_to_hash);


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
            $person->save();
            $person->adress()->save($adress);
            $person->contactinformation()->save($contact);
            $person->adress()->country = $country->country_id;
        }


        $reg_uid = Uuid::uuid4();
        $registration = new Registration();
        $registration->registration_uid = $reg_uid;
        // banans uid hårdkoda tills vi bygg ut möjlighet att överföra från brevet applikationen
        $registration->course_uid = $event->event_uid;
        $registration->person_uid = $person->person_uid;
        $registration->additional_information = $request['extra-info'];

        $reg_product = Product::find($request->input('save'));

        // sätt reserve eller complete baserat på categori. 7 är reservation och 6 är registrering
        if ($reg_product->categoryID === 7) {
            $registration->reservation = true;
            $registration->reservation_valid_until = $event->eventconfiguration->reservationconfig->use_reservation_until;
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

        $person->registration()->save($registration);
        $reg = Registration::find($registration->registration_uid);


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

            if (strval($request['dinner']) == strval($product['productID'])) {
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


            if (strval($request['medal']) == strval($product['productID'])) {
                $optional = new Optional();
                $optional->registration_uid = $reg->registration_uid;
                $optional->productID = $product['productID'];
                $optional->save();
            }


        }


        if (App::isProduction()) {
            $use_stripe = env("USE_STRIPE_PAYMENT_INTEGRATION");
            if ($use_stripe) {
                return to_route('checkout', ["reg" => $reg->registration_uid, 'price_id' => $reg_product->price_id, 'event_type' => $event->event_type]);
            } else {
                event(new CompletedRegistrationSuccessEvent($registration));
                return to_route('checkoutsuccess', ["reg" => $reg->registration_uid, 'price_id' => $reg_product->price_id, 'event_type' => $event->event_type]);
            }
        } else {
            $use_stripe = env("USE_STRIPE_PAYMENT_INTEGRATION");
            if ($use_stripe) {
                if ($reg_product->categoryID === 7) {
                    $price = env("STRIPE_TEST_PRODUCT_RESERVATION");
                } else {
                    $price = env("STRIPE_TEST_PRODUCT");
                }
                return to_route('checkout', ["reg" => $reg->registration_uid, 'price_id' => $price, "event_type" => $event->event_type]);

            } else {
                event(new CompletedRegistrationSuccessEvent($registration));
                return to_route('checkoutsuccess', ["reg" => $reg->registration_uid, 'price_id' => $reg_product->price_id, 'event_type' => $event->event_type]);

            }
        }

    }

    private function isExistingregistrationWithTelOnCourse(string $tel, string $course_uid): bool
    {
        $contact = Contactinformation::where('tel', $tel)->get()->first();
        if ($contact) {
            $regs = Registration::where('course_uid', $course_uid)->get();
            foreach ($regs as $reg) {
                if (strtolower($reg->person->contactinformation->tel) == strtolower($tel)) {
                    break;
                }
            }
            return true;
        }
        return false;
    }
}
