<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased bg-stone-100">
<!-- Header -->
<header class="bg-white py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2024.svg') }}"/>
    </div>
</header>
<!-- Main Content -->
<div class="container mx-auto p-0 font-sans">
    <div class="bg-orange-50 p-4 shadow-md">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>

                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Something went wrong</strong>
                    @foreach ($errors->all() as $error)
                    <span class="block sm:inline"><li>{{ $error }}</li></span>
                    @endforeach

                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 20 20"><title>Close</title><path
            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
  </span>
                </div>
            </ul>
        </div>
        @endif


        <p class="mb-5">If you are unsure whether you will have the opportunity to participate, you can reserve a
            starting place until the 31st of December. The reservation fee is 20 EUR (not refunded or deducted from the registration
            fee).</p>

        <p class="mb-5 text-orange-500 font-medium"> Please note, all reservations are cancelled automatically on the 1st of January.
            Make your final registration before New Year's Eve.</p>

        <p class="mb-5">If you have decided to participate, you can register immediately. The registration fee is 250 EUR until the 31st
            of December. Thereafter 300 EUR until the 2nd of June. Then finally 350 EUR for last-minute tickets until the 14th of
            June. Registration fees are not refunded however starting places can be resold and transfered until the 2nd of June.</p>

        <p class="text-left  mb-4">
            Please fill in the registration form below with your name and date of birth, the address to which your medal from Les
            Randonneurs Mondiaux will be sent upon completing the randonnée, the mobile number that you’ll be using throughout the
            event, a permanent email address
            and also any special dietary requirements you may have (gluten free, lactose free, allergies, vegan, vegetarian etc).
        </p>

        <p>After submitting your entry please check that you have received a confirmation by email. If not then check your spam folder
            and if found there, please change your spam filter settings for the address "info@midnightsunrandonnee.se" so you will not
            miss future emails.</p>

        <form method="post" class="grid sm:grid-cols-1 gap-4">
            @csrf

            <hr class="h-1 my-4 bg-gray-900 border-0 dark:bg-gray-700">
            <div class="border-gray-900/10 pb-3">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">First name</label>
                        <input type="text" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Last name</label>
                        <input type="text" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">

                        <label for="email" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email address*</label>
                        <input id="email" name="email" type="email" autocomplete="email"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="email-confirm" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email
                            confirmation*</label>
                        <input id="email-confirm" name="email-confirm" type="email"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="tel" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Mobile number</label>
                    <input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
                           class=" w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-adress" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Street
                        adress</label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Postal
                            code</label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               class=" w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">City</label>
                        <input type="text" name="city" id="city" autocomplete="address-level2"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="country" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Country</label>
                    <select id="country" name="country" autocomplete="country-name"
                            class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600"
                            required>
                        <option>Select country</option>
                        @foreach ($countries as $country)
                        <option value="{{$country->country_id}}">
                            {{$country->country_name_en}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-2 mb-4">
                    <label for="club" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Club</label>
                    <input type="text" name="club" id="club"
                           class="md:w-1/2  sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <p class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Birthdate</p>
                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
                        <div class="mt-2">

                            <select name="year" id="year"
                                    class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                                <option>Year</option>
                                @foreach ($years as $year)
                                <option value="{{$year}}">
                                    {{$year}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-2">

                            <select name="month" id="month"
                                    class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                                <option value="">Month</option>
                                <option value="01">Januray</option>
                                <option value="02">February</option>
                                <option value="03">March</option>
                                <option value="04">April</option>
                                <option value="05">May</option>
                                <option value="06">June</option>
                                <option value="07">July</option>
                                <option value="08">August</option>
                                <option value="09">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>
                        <div class="mt-2">

                            <select name="day" id="day"
                                    class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                                <option value="">Day</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="16">16</option>
                                <option value="17">17</option>
                                <option value="18">18</option>
                                <option value="19">19</option>
                                <option value="20">20</option>
                                <option value="21">21</option>
                                <option value="22">22</option>
                                <option value="23">23</option>
                                <option value="24">24</option>
                                <option value="25">25</option>
                                <option value="26">26</option>
                                <option value="27">27</option>
                                <option value="28">28</option>
                                <option value="29">29</option>
                                <option value="30">30</option>
                                <option value="31">31</option>
                            </select>
                        </div>

                    </div>

                </div>


                <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Special dietary
                        requirements</label>
                    <textarea id="extra-info" name="extra-info" rows="1"
                              class="sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"></textarea>
                </div>
                <BR>
                <hr class="h-1 bg-gray-200 border-0 dark:bg-gray-700">
                <fieldset class="mt-5 mb-5">
                    <legend class="text-xl font-semibold leading-6 text-gray-900">Carpool from Europe</legend>
                    <p class="mt-1 text-base leading-6 text-gray-600">If you plan to drive to Umeå and would like to share a ride,
                        please add your name to the carpool.</p>
                    <div class="mt-6 space-y-1">
                        <div class="flex items-center gap-x-3">
                            <input id="driver" name="productID" value="1009" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-gray-600">
                            <label for="driver" class="block sm:text-base font-sm leading-6 text-gray-900">Driver looking for
                                passengers</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="vehicle" name="productID" value="1010" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-gray-600">
                            <label for="vehicle" class="block sm:text-base font-sm leading-6 text-gray-900">Passenger looking for
                                vehicle</label>
                        </div>
                    </div>
                </fieldset>
                <hr class="h-1 my-8 bg-gray-200 border-0 dark:bg-gray-700">
                <fieldset>
                    <legend class="text-xl font-semibold leading-6 text-gray-900">Included in the entry fee</legend>
                    <p class="mt-1 text-base leading-6 text-gray-600">Please look throught the event program before you submit your
                        choices.</p>
                    <div class="mt-6 space-y-1">
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="pre_event_coffee_ride" name="1000" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-2">
                                <label for="pre_event_coffee_ride" class="font-sm sm:text-base text-gray-900">Pre-event coffee ride -
                                    Scandic Plaza</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="lunch_box" name="1001" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">

                            </div>
                            <div class="text-sm leading-6">
                                <label for="lunch_box" class="font-sm sm:text-base text-gray-900">Lunch box - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="bag_drop" name="1002" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="bag_drop" class="font-sm sm:text-base text-gray-900">Bag drop - Baggböle Manor (to Scandic
                                    Plaza)</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="long_term_parking" name="1003" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="long_term_parking" class="font-sm sm:text-base text-gray-900">
                                    Long-term parking - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="buffet_dinner" name="1004" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="buffet_dinner" class="font-sm sm:text-base text-gray-900">Buffet Dinner- Brännland
                                    Inn</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="midsummer" name="1005" type="checkbox"
                                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="midsummer" class="font-sm sm:text-base text-gray-900">Swedish Midsummer Celebration -
                                    Norrmjöle</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr class="h-1 my-8 bg-gray-200 border-0 dark:bg-gray-700">
                <fieldset class="mt-5">
                    <legend class="text-xl font-semibold leading-6 text-gray-900">Not included in the entry fee</legend>
                    <p class="mt-1 text-base leading-6 text-gray-600">Purchase a digital voucher for the MSR jersey at a 20% discount.
                        The voucher can be used as payment on the La Chemise website until the 7th of July.</p>
                    <div class="mt-6 space-y-1">
                        <div class="flex items-center gap-x-3">
                            <input id="malefemale-tor" name="jersey" value="1008" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
                            <label for="malefemale-tor" class="block sm:text-base font-sm leading-6 text-gray-900">TOR 3.0 Jersey F/M
                                (107 EUR on webshop -20%): 86 EUR</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="malefemale-grand" name="jersey" value="1007" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
                            <label for="malefemale-grand" class="block sm:text-base font-sm leading-6 text-gray-900">GRAND Jersey F/M
                                (87 EUR on webshop -20%): 70 EUR</label>
                        </div>
                    </div>
                </fieldset>
                <hr class="h-1 my-12 bg-gray-900 border-0 dark:bg-gray-700">

                <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                    <button type="submit" value="reserve" name="save"
                            class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600">
                        Reserve
                    </button>
                    <button type="submit" value="Registration" name="save"
                            class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600">
                        Register
                    </button>
                </div>
                <p class="text-xs mt-10">
                    Please note, the organiser Cykelintresset is under no obligation to refund a participant who for any reason is
                    unable to participate in the event. Entry tickets may however be resold. Emergency roadworks, extreme weather and
                    other unforeseen circumstances may force the organiser to make last minute changes and adjustments to the event,
                    including the course, the checkpoints and services at the checkpoints. The organiser reserves the right to make
                    these changes without compensation to participants. If for some unforseen reason the event cannot take place in June
                    2024 the event will not be cancelled but instead be postponed until 2025. By registering, you accept the
                    above-mentioned terms and conditions.
                </p>
            </div>
        </form>
    </div>
</div>
</body>

</html>
