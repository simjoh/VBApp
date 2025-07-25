<?php

namespace App\Http\Controllers;

use App\Events\CompletedRegistrationSuccessEvent;
use App\Events\UpdateCompetitorInfoEvent;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class RegistrationController extends Controller
{
    use MonthsTrait;
    use DaysTrait;
    use HashTrait;
    use GenderTrait;

    // Use class constants instead of enum
    private const PRODUCT_REGISTRATION = 6;
    private const PRODUCT_RESERVATION = 7;

    public function index(Request $request)
    {

        $eventType = $request->query('event_type');


        if (!Str::isUuid($request['uid'])) {
            return view('registrations.updatesuccess')->with(['text' => __('Invalid request')]);
        }
        $event = Event::find($request['uid']);

        if (!$event) {
            return view('registrations.updatesuccess')->with(['text' => __('You tried to access registration form for non-existing event')]);
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

        $registrationopen = Carbon::parse($event->eventconfiguration->registration_opens);
        $isRegistrationOpen = Carbon::now()->gt($registrationopen);


        if ($eventType === 'BRM' || $eventType === 'BP') {

            $registrationConfig = [
                'opens' => strtoupper(Carbon::parse($event->eventconfiguration->registration_opens)->format('d') . ' ' . $this->monthsforSelect()[Carbon::parse($event->eventconfiguration->registration_opens)->month]),
                'closes' => strtoupper(Carbon::parse($event->eventconfiguration->registration_closes)->format('d') . ' ' . $this->monthsforSelect()[Carbon::parse($event->eventconfiguration->registration_closes)->month]),
                'isRegistrationOpen' => $isRegistrationOpen,
                'event_uid' => $event->event_uid,
                'event_name' => $event->title,
                'event_type' => $event->event_type,
                'startdate' => Carbon::parse($event->startdate)->format('Y-m-d'),
            ];

            // Add clubs query for BRM events

            $clubs = Club::where('official_club', true)
                ->orWhere('acp_code', 'LIKE', 'SE%')
                ->orderBy('name')
                ->get();

            return view('registrations.brevet')->with([
                'showreservationbutton' => $reservationactive,
                'countries' => Country::all()->sortBy("country_name_sv"),
                'event' => $event->event_uid,
                'event_type' => $event->event_type,
                'years' => range(date('Y', strtotime('-18 year')), 1950),
                'registrationproduct' => $registration_product->productID,
                'reservationproduct' => $reservationactive == false ? null : $resevation_product->productID,
                'genders' => $this->gendersSv(),
                'isRegistrationOpen' => $isRegistrationOpen,
                'availabledetails' => $registrationConfig,
                'clubs' => $clubs  // Add clubs to the view data
            ]);
        }

        $registrationConfig = [
            'opens' => \strtoupper(Carbon::parse($event->eventconfiguration->registration_opens)->format('d F')),
            'closes' => \strtoupper(Carbon::parse($event->eventconfiguration->registration_closes)->format('d F')),
            'isRegistrationOpen' => $isRegistrationOpen,
            'event_uid' => $event->event_uid,
            'event_name' => $event->title,
            'event_type' => $event->event_type,
            'startdate' => Carbon::parse($event->startdate)->format('Y-m-d'),
        ];
        return view('registrations.show')->with([
            'showreservationbutton' => $reservationactive,
            'countries' => Country::all()->sortBy("country_name_en"),
            'years' => range(date('Y', strtotime('-18 year')), 1950),
            'registrationproduct' => $registration_product->productID,
            'reservationproduct' => $reservationactive == false ? null : $resevation_product->productID,
            'genders' => $this->gendersEn(),
            'isRegistrationOpen' => $isRegistrationOpen,
            'availabledetails' => $registrationConfig
        ]);
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
        $adress->country_id = $request['country'];
        $person->adress->update();

        $contact = $person->contactinformation;
        $contact->tel = $request['tel'];
        $contact->email = $contact->email;
        $person->contactinformation->update();

        $registration->additional_information = $request['extra-info'];

        // Update use_physical_brevet_card field for BRM and BP events
        $event = Event::where('event_uid', $registration->course_uid)->first();
        if ($event && ($event->event_type === 'BRM' || $event->event_type === 'BP')) {
            $registration->use_physical_brevet_card = $request->has('use_physical_brevet_card') && $request->input('use_physical_brevet_card') == '1';
            // This is a BRM event
            $registration->club_uid = $request['club_uid'];
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


        // Fire event to update competitor info in cycling app
        event(new UpdateCompetitorInfoEvent($registration->registration_uid, $person->person_uid));

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

        // Get the registration model to access the relationship with event
        $registrationModel = Registration::where('registration_uid', $request['registrationUid'])->first();

        // Get the event using the course_uid from the registration
        $event = Event::where('event_uid', $registrationModel->course_uid)->first();

        $optionalsforreg = Optional::where('registration_uid', $registration->registration_uid)->pluck('productID');

        // Default clubs list - all clubs sorted by name
        $clubs = Club::orderBy('name')->get();

        $viewData = [
            'countries' => Country::all()->sortByDesc("country_name_en"),
            'years' => range(date('Y'), 1950),
            'days' => $this->daysforSelect(),
            'months' => $this->monthsforSelect(),
            'registration' => $registration,
            'day' => $day,
            'month' => $month,
            'birthyear' => $year,
            'optionalsforregistration' => $optionalsforreg,
            'genders' => $this->gendersEn(),
            'clubs' => $clubs
        ];

        // Check if the event exists and its type is BRM
        if ($event && ($event->event_type === 'BRM' || $event->event_type === 'BP')) {
            // For BRM events, only show official clubs or clubs with ACP codes
            $brmClubs = Club::where('official_club', true)
                ->orWhere('acp_code', 'LIKE', 'SE%')
                ->orderBy('name')
                ->get();

            $viewData['clubs'] = $brmClubs;

            // Return a different blade view for BRM event type
            return view('registrations.edit_brm')->with($viewData);
        }

        // Return the default blade view
        return view('registrations.edit')->with($viewData);
    }


    public function create(Request $request): RedirectResponse
    {
        $event = $this->validateEventExists($request['uid']);
        if (!$event) {
            abort(404, 'Event not found');
        }

        $this->validateRegistrationRequest($request);

        if (!$this->isRegistrationOpen($event)) {
            return back()->withErrors(['registration_closes' => 'Registrering är stängd'])->withInput();
        }

        try {
            // Use transaction only for the data creation part
            $registration = DB::transaction(function () use ($request, $event) {
                $person = $this->handlePersonData($request, $event);
                $registration = $this->createRegistration($request, $event, $person);
                $this->handleClubAssignment($request, $event, $registration);
                $this->processOptionalProducts($request, $registration);

                return $registration;
            });

            // Handle payment redirect outside of transaction so registration is committed
            return $this->handlePaymentRedirect($event, $registration);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    private function validateEventExists(string $eventUid): ?Event
    {
        return Event::where('event_uid', $eventUid)->first();
    }

    private function validateRegistrationRequest(Request $request): array
    {
        $event = Event::where('event_uid', $request['uid'])->first();
        $isMSR = $event && $event->event_type === 'MSR';

        // Convert string month to integer
        if (isset($request['month'])) {
            $request->merge(['month' => intval($request['month'])]);
        }

        $messages = $isMSR ? [
            'country.integer' => 'Please select a country from the list',
            'country.exists' => 'Please select a valid country from the list',
            'year.integer' => 'Please select a valid year',
            'year.between' => 'Age must be at least 10 years old',
            'month.integer' => 'Please select a valid month',
            'month.between' => 'Month must be between 1 and 12',
            'day.integer' => 'Please select a valid day',
            'day.between' => 'Day must be between 1 and 31',
            'email.regex' => 'Please enter a valid email address',
            'email-confirm.regex' => 'Please enter a valid confirmation email address'
        ] : [
            'country.integer' => 'Vänligen välj ett land från listan',
            'country.exists' => 'Vänligen välj ett giltigt land från listan',
            'year.integer' => 'Vänligen välj ett giltigt år',
            'year.between' => 'Du måste vara minst 10 år gammal',
            'month.integer' => 'Vänligen välj en giltig månad',
            'month.between' => 'Månad måste vara mellan 1 och 12',
            'day.integer' => 'Vänligen välj en giltig dag',
            'day.between' => 'Dag måste vara mellan 1 och 31',
            'email.regex' => 'Vänligen ange en giltig e-postadress',
            'email-confirm.regex' => 'Vänligen ange en giltig bekräftelse-e-postadress'
        ];

        return $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'country' => 'required|integer|exists:countries,country_id',
            'tel' => 'required|string|max:100',
            'street-address' => 'required|string|max:100',
            'postal-code' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'email-confirm' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'year' => 'required|integer|between:1950,' . date('Y', strtotime('-10 year')),
            'month' => 'required|integer|between:1,12',
            'day' => 'required|integer|between:1,31'
        ], $messages);
    }

    private function isRegistrationOpen(Event $event): bool
    {
        $registration_closes = Carbon::parse($event->eventconfiguration->registration_closes);
        return $registration_closes->gt(Carbon::now());
    }

    private function handlePersonData(Request $request, Event $event): Person
    {
        $string_to_hash = strtolower($request['first_name']) . strtolower($request['last_name']) . strtolower($request['year'] . "-" . $request['month'] . "-" . $request['day']);

        if (Person::where('checksum', $this->hashsumfor($string_to_hash))->exists()) {
            return $this->updateExistingPerson($request, $string_to_hash, $event);
        } else {
            return $this->createNewPerson($request, $string_to_hash);
        }
    }

    private function updateExistingPerson(Request $request, string $string_to_hash, Event $event): Person
    {
        $person = Person::where('checksum', $this->hashsumfor($string_to_hash))->first();

        // Check if already registered for this event
        $registrationsforperson = $person->registration;
        if ($registrationsforperson) {
            foreach ($registrationsforperson as $registration) {
                if ($registration->course_uid === $request['uid']) {
                    throw new \Exception('You already registered on event');
                }
            }
        }

        $person->gender = $request['gender'];
        $person->gdpr_approved = $request['gdpr'] === 'on';

        $this->updatePersonAddress($person, $request);
        $this->updatePersonContact($person, $request);

        return $person;
    }

    private function createNewPerson(Request $request, string $string_to_hash): Person
    {
        $person = new Person();
        $person->person_uid = Uuid::uuid4();
        $person->firstname = Str::of($request['first_name'])->ucfirst();
        $person->surname = Str::of($request['last_name'])->ucfirst();
        $person->gender = $request['gender'];
        $person->birthdate = $request['year'] . "-" . str_pad($request['month'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($request['day'], 2, "0", STR_PAD_LEFT);
        $person->registration_registration_uid = '1111111';
        $person->checksum = $this->hashsumfor($string_to_hash);
        $person->gdpr_approved = $request['gdpr'] === 'on';

        $person->save();

        $this->createPersonAddress($person, $request);
        $this->createPersonContact($person, $request);

        return $person;
    }

    private function updatePersonAddress(Person $person, Request $request): void
    {
        $address = $person->adress;
        if ($address === null) {
            $address = new Adress();
            $address->adress_uid = Uuid::uuid4();
            $address->person_person_uid = $person->person_uid;
        }

        $address->adress = $request['street-address'];
        $address->postal_code = $request['postal-code'];
        $address->country_id = $request['country'];
        $address->city = $request['city'];

        if ($person->adress === null) {
            $person->adress()->save($address);
        } else {
            $address->save();
        }
    }

    private function updatePersonContact(Person $person, Request $request): void
    {
        if ($person->contactinformation) {
            $contact = $person->contactinformation;
            $contact->tel = $request['tel'];
            $contact->email = $request['email'];
            $contact->save();
        } else {
            $this->createPersonContact($person, $request);
        }
    }

    private function createPersonAddress(Person $person, Request $request): void
    {
        $address = new Adress();
        $address->adress_uid = Uuid::uuid4();
        $address->adress = $request['street-address'];
        $address->postal_code = $request['postal-code'];
        $address->country_id = $request['country'];
        $address->city = $request['city'];
        $address->person_person_uid = $person->person_uid;

        $person->adress()->save($address);
    }

    private function createPersonContact(Person $person, Request $request): void
    {
        $contact = new Contactinformation();
        $contact->contactinformation_uid = Uuid::uuid4();
        $contact->tel = $request['tel'];
        $contact->email = $request['email'];

        $person->contactinformation()->save($contact);
    }

    private function createRegistration(Request $request, Event $event, Person $person): Registration
    {
        $reg_product = Product::find($request->input('save'));
        if (!$reg_product) {
            throw new \Exception('Invalid product selected.');
        }

        $registration = new Registration();
        $registration->registration_uid = Uuid::uuid4();
        $registration->course_uid = $event->event_uid;
        $registration->person_uid = $person->person_uid;
        $registration->additional_information = $request['extra-info'];

        // Set use_physical_brevet_card field for BRM and BP events
        if ($event->event_type === 'BRM' || $event->event_type === 'BP') {
            $registration->use_physical_brevet_card = $request->has('use_physical_brevet_card') && $request->input('use_physical_brevet_card') == '1';
        } else {
            $registration->use_physical_brevet_card = false;
        }

        // Set reservation status based on product category (7 = reservation, 6 = registration)
        if ($reg_product->categoryID === self::PRODUCT_RESERVATION) {
            $registration->reservation = true;
            $registration->reservation_valid_until = $event->eventconfiguration->reservationconfig->use_reservation_until;
        } else {
            $registration->reservation = false;
        }

        $person->registration()->save($registration);

        return $registration;
    }

    private function handleClubAssignment(Request $request, Event $event, Registration $registration): void
    {
        if ($event->event_type === 'BRM' || $event->event_type === 'BP') {
            $this->handleBrmClubAssignment($request, $registration);
        } else {
            $this->handleMsrClubAssignment($request, $registration);
        }
    }

    private function handleBrmClubAssignment(Request $request, Registration $registration): void
    {
        $club = Club::where('club_uid', $request['club_uid'])->first();

        if (!$club) {
            throw new \Exception('The selected club does not exist.');
        }

        // Validate that the club is official or has SE ACP code
        if (!$club->official_club && !str_starts_with($club->acp_code ?? '', 'SE')) {
            throw new \Exception('For BRM OR BP events, you must select an official club recognized by Audax Club Parisien.');
        }

        $registration->club_uid = $club->club_uid;
        $registration->save();
    }

    private function handleMsrClubAssignment(Request $request, Registration $registration): void
    {
        $club = Club::where(DB::raw('LOWER(name)'), 'LIKE', strtolower(trim($request['club'])) . '%')->first();

        if (!$club) {
            $club = new Club();
            $club->club_uid = Uuid::uuid4();
            $club->name = $request['club'];
            $club->description = null;
            $club->official_club = false;
            $club->save();
        }

        $registration->club_uid = $club->club_uid;
        $registration->save();
    }

    private function processOptionalProducts(Request $request, Registration $registration): void
    {
        $productIds = Product::pluck('productID')->toArray();
        $optionalFields = ['productID', 'dinner', 'medal', 'jersey'];

        foreach ($productIds as $productId) {
            $shouldAddOptional = false;

            // Check checkbox selections
            if ($request[$productId] === 'on') {
                $shouldAddOptional = true;
            }

            // Check specific field matches
            foreach ($optionalFields as $field) {
                if (strval($request[$field]) === strval($productId)) {
                    $shouldAddOptional = true;
                    break;
                }
            }

            if ($shouldAddOptional) {
                $optional = new Optional();
                $optional->registration_uid = $registration->registration_uid;
                $optional->productID = $productId;
                $optional->save();
            }
        }
    }

    private function handlePaymentRedirect(Event $event, Registration $registration): RedirectResponse
    {
        $reg_product = Product::find(request()->input('save'));
        $use_stripe = $this->shouldUseStripe($event);

        if ($use_stripe) {
            $price_id = $this->getStripePrice($reg_product);
            return to_route('checkout', [
                "reg" => (string) $registration->registration_uid,
                'price_id' => $price_id,
                "event_type" => $event->event_type
            ]);
        } else {
            event(new CompletedRegistrationSuccessEvent($registration));
            return to_route('checkoutsuccess', [
                "reg" => (string) $registration->registration_uid,
                'price_id' => $reg_product->price_id,
                'event_type' => $event->event_type
            ]);
        }
    }

    private function shouldUseStripe(Event $event): bool
    {
        return isset($event->eventConfiguration) && isset($event->eventConfiguration->use_stripe_payment)
            ? $event->eventConfiguration->use_stripe_payment
            : env("USE_STRIPE_PAYMENT_INTEGRATION");
    }

    private function getStripePrice(Product $reg_product): string
    {
        if (App::isProduction()) {
            return $reg_product->price_id;
        } else {
            return $reg_product->categoryID === 7
                ? env("STRIPE_TEST_PRODUCT_RESERVATION")
                : env("STRIPE_TEST_PRODUCT");
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
