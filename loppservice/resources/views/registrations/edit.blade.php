<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased">
<!-- Header -->
<header class="bg-blue-500 py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="700" height="800" src="{{ asset('logo-2024.svg') }}"/>
    </div>
</header>
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
        <h2 class="text-2xl font-semibold mb-4">Update your details</h2>
        <form method="post" class="grid sm:grid-cols-1 gap-4" action="{{ url('registration.update') }}">
            @method('PUT')
            @csrf
            <input type="text" value="{{ $registration->registration_uid }}" hidden="hidden" id="registration_uid"
                   name="registration_uid">
            <div class="border-b border-gray-900/10 pb-12">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Firstname</label>
                        <input type="text" value="{{ $registration->firstname }}" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Last name</label>
                        <input type="text" value="{{ $registration->surname }}" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="email" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{$registration->email}}"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-adress" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Street adress</label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           value="{{ $registration->adress}}"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Zip/postal code</label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               value="{{ $registration->postal_code}}"
                               class=" w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">City</label>
                        <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ $registration->city}}"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="tel" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Tel</label>
                        <input type="text" name="tel" id="tel" autocomplete="tel" value="{{$registration->tel}}"
                               class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="club" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Club</label>
                        <input type="text" name="club" id="club" value="{{$registration->club_name}}"
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
                                @if ($year == $birthyear)
                                <option value="{{ $year }}" selected>{{ $year }}</option>
                                @else
                                <option value="{{ $year }}">{{ $year }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-2">
                            <label for="month" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">MM</label>
                            <select name="month" id="month"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                @foreach ($months as $key => $months)
                                     @if ($key == $month)
                                             <option value="{{ $key }}" selected>{{ $months }}</option>
                                     @else
                                             <option value="{{ $key }}">{{ $months }}</option>
                                     @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-2">
                            <label for="day" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">DD</label>
                            <select name="day" id="day"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                <option value="">Day</option>
                                @foreach ($days as $daysa)
                                 @if ($daysa == $day)
                                    <option value="{{ $daysa }}" selected>{{ $daysa }}</option>
                                 @else
                                     <option value="{{ $daysa }}">{{ $daysa }}</option>
                                 @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Special Dietary
                        Requirements</label>
                    <textarea id="extra-info" name="extra-info" rows="5"
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600">
                    {{$registration->additional_information}}

                </textarea>
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
                                <label for="pre_event_coffee_ride" class="font-medium text-gray-900">Pre-event coffee ride - Scandic Plaza</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="lunch_box" name="1001" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="lunch_box" class="font-medium sm:text-sm text-gray-900">Lunch box - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="bag_drop" name="1002" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="bag_drop" class="font-medium sm:text-sm text-gray-900">Bag drop - Baggböle Manor (to Scandic Plaza)</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="long_term_parking" name="1003" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="long_term_parking" class="font-medium sm:text-sm text-gray-900">
                                    Long-term parking - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="buffet_dinner" name="1004" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="buffet_dinner" class="font-medium sm:text-sm text-gray-900">Buffet Dinner- Brännland Inn</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="midsummer" name="1005" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600">
                            </div>
                            <div class="text-sm leading-6">
                                <label for="midsummer" class="font-medium sm:text-sm text-gray-900">Swedish Midsummer Celebration - Norrmjöle</label>
                            </div>
                        </div>
                    </div>
                </fieldset>


                <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                    <button type="submit" value="reserve" name="save"
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-gren-700 focus:outline-none focus:bg-green-600">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
