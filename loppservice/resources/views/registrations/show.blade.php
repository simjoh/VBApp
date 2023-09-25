<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased">
<!-- Header -->
<header class="bg-blue-500 py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="700" height="800"  src ="/logo-2024.svg"/>
    </div>
</header>
<!-- Main Content -->
<div class="container mx-auto p-4">
    <div class="bg-white p-6 rounded-md shadow-md">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <h2 class="text-2xl mb-4">Registration form</h2>
        <p class="text-left  mb-4">
            Please fill in the registration form below with your name and date of birth, the address to which your medal from Les
            Randonneurs Mondiaux will be sent upon completing the randonnée, the mobile number that you’ll be using throughout the event
            and also any special dietary requirements you may have (gluten free, lactose free, allergies, vegan, vegetarian etc).
        </p>

        <p class="text-left mb-4">
            If you are registering a reserved starting place, please enter your start number and check that your personal information
            and choices are still valid before confirming your registration.

        </p>
        <p class="mt-1 text-sm leading-6 text-gray-600">Use a permanent address where you can receive mail.</p>
        <form method="post" class="grid sm:grid-cols-1 gap-4">
            @csrf
            <div class="border-b border-gray-900/10 pb-12">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Firstname</label>
                        <input type="text" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Last name</label>
                        <input type="text" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="email" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                </div>
                <div class="mt-2 w-1/2">
                    <label for="email-confirm" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Repaeat your
                        email</label>
                    <input id="email-confirm" name="email-confirm" type="email"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-adress" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Street adress</label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Zip/postal code</label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               class=" w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">City</label>
                        <input type="text" name="city" id="city" autocomplete="address-level2"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="tel" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Tel</label>
                        <input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
                               class=" w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="club" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Club</label>
                        <input type="text" name="club" id="club"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>

                </div>
                <p class="text-sm font-medium leading-6 text-gray-900">Birthdate</p>
                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">

                    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">

                        <div class="mt-2">
                            <label for="year" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">YYYY</label>
                            <select name="year" id="year"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                <option>Year</option>
                                @foreach ($years as $year)
                                <option value="{{$year}}">
                                    {{$year}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-2">
                            <label for="month" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">MM</label>
                            <select name="month" id="month"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
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
                            <label for="day" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">DD</label>
                            <select name="day" id="day"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
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

                <div class="mt-3">
                    <label for="country" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Country</label>
                    <select id="country" name="country" autocomplete="country-name"
                            class="sm:w-full md:w-1/2 lg:w-1/2 py-2 border rounded-md focus:outline-none focus:border-blue-600"
                            required>
                        <option>Select country</option>
                        @foreach ($countries as $country)
                        <option value="{{$country->country_id}}">
                            {{$country->country_name_en}}
                        </option>
                        @endforeach
                    </select>
                </div>


                <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Special Dietary
                        Requirements</label>
                    <textarea id="extra-info" name="extra-info" rows="5"
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600"></textarea>
                </div>

                <fieldset>
                    <legend class="text-sm font-medium leading-6 text-gray-900">Included in the entry fee</legend>
                    <div class="mt-6 space-y-6">
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="pre_event_coffee_ride" name="1000" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="pre_event_coffee_ride" class="font-medium text-gray-900">Pre-event coffee ride - Umeå
                                    Plaza, Saturday 15 June, 10:00.</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="lunch_box" name="1001" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="lunch_box" class="font-medium sm:text-sm text-gray-900">Lunch box - Baggböle Manor, Sunday
                                    16 June,
                                    15:00.</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="bag_drop" name="1002" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="bag_drop" class="font-medium sm:text-sm text-gray-900">Bag drop Umeå Plaza - Baggböle Manor,
                                    Sunday
                                    16 June, 15:00.</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="long_term_parking" name="1003" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="long_term_parking" class="font-medium sm:text-sm text-gray-900">Long-term parking - Baggböle
                                    Manor,
                                    Sunday 16 June - Thursday 20 June.</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="buffet_dinner" name="1004" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="buffet_dinner" class="font-medium sm:text-sm text-gray-900">Buffet Dinner- Brännland Inn,
                                    Sunday 16
                                    June, 19:00.</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="midsummer" name="1005" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="midsummer" class="font-medium sm:text-sm text-gray-900">Swedish Midsummer Celebration -
                                    Friday 20
                                    June, 12:00.</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mt-5">
                    <legend class="text-sm font-semibold leading-6 text-gray-900">Not included in the entry fee</legend>
                    <p class="mt-1 text-sm leading-6 text-gray-600">These are delivered via SMS to your mobile phone.</p>
                    <div class="mt-6 space-y-6">
                        <div class="flex items-center gap-x-3">
                            <input id="female-core" name="jersey" value="1007" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="female-core" class="block sm:text-sm font-medium leading-6 text-gray-900">MSR Jersey - Female,
                                Core Fittet, 680 SEK (-25%)</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="female-tor" name="jersey" value="1008" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="female-tor" class="block sm:text-sm font-medium leading-6 text-gray-900">MSR Jersey - Female,
                                Tor, 980 SEK (-25%)</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="male-core" name="jersey" value="1009" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="male-core" class="block sm:text-sm font-medium leading-6 text-gray-900">MSR Jersey - Male, Core
                                Fittet, 680 SEK (-25%)</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="male-tor" name="jersey" value="1010" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <label for="male-tor" class="block sm:text-sm font-medium leading-6 text-gray-900">MSR Jersey - Male, Tor,
                                980 SEK (-25%)</label>
                        </div>
                    </div>
                </fieldset>


                <fieldset class="mt-5">
                    <legend class="text-sm font-semibold leading-6 text-gray-900">Carpool from Europe</legend>
                    <p class="mt-1 text-sm leading-6 text-gray-600">These are delivered via SMS to your mobile phone.</p>
                    <div class="mt-6 space-y-6">
                        <div class="flex items-center gap-x-3">
                            <input id="driver" name="productID" value="1011" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-blue-600">
                            <label for="driver" class="block sm:text-sm font-medium leading-6 text-gray-900">Driver looking for
                                passengers</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="vehicle" name="productID" value="1012" type="radio"
                                   class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-blue-600">
                            <label for="vehicle" class="block sm:text-sm font-medium leading-6 text-gray-900">Passenger looking for
                                vehicle</label>
                        </div>
                    </div>
                </fieldset>

                <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                    <button type="submit" value="reserve" name="save"
                            class="w-full bg-green-500 text-white py-2 px-4 font-bold rounded-md hover:bg-green-600 focus:outline-none focus:bg-blue-600">
                        Reserve
                    </button>
                    <button type="submit" value="Registration" name="save"
                            class="w-full bg-green-500 text-white py-2 px-4 font-bold rounded-md hover:bg-green-600 focus:outline-none focus:bg-green-600">
                        Register
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>

</html>

