<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<header class="bg-gray-200 py-0">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2025.svg') }}"/>
    </div>
</header>
<body class="antialiased bg-stone-100">
<!-- Main Content -->
<div class="container mx-auto p-0 font-sans">
    <div class="bg-orange-50 p-6 rounded-md shadow-md">
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
        <h2 class="text-2xl font-semibold mb-4">Update your details</h2>

        <form method="post" class="grid sm:grid-cols-1 gap-4" action="{{ url('registration.update') }}">
            @method('PUT')
            @csrf
            <hr class="h-1 my-4 bg-gray-900 border-0 dark:bg-gray-700">
            <input type="text" value="{{ $registration->registration_uid }}" hidden="hidden" id="registration_uid"
                   name="registration_uid">
            <div class="border-gray-900/10 pb-3">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Firstname</label>
                        <input type="text" value="{{ $registration->firstname }}" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Last name</label>
                        <input type="text" value="{{ $registration->surname }}" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="email" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Email</label>
                    <input id="email" name="email" disabled type="email" autocomplete="email" value="{{$registration->email}}"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-address" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Street adress</label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           value="{{ $registration->adress}}"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Zip/postal code</label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               value="{{ $registration->postal_code}}"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">City</label>
                        <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ $registration->city}}"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="mt-2 mb-4 md:w-1/2 sm:w-full">
                    <label for="country" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Country</label>
                    <select name="country" id="country" autocomplete="country-name"
                            class="w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                        <option>Select country</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->country_id }}" {{ $registration->country_id == $country->country_id ? 'selected' : '' }}>
                                {{ $country->country_name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="tel" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Tel</label>
                        <input type="text" name="tel" id="tel" autocomplete="tel" value="{{$registration->tel}}"
                               class="md:w-full  sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="club" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Club</label>
                        <select name="club_uid" id="club" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->club_uid }}" {{ $registration->club_uid == $club->club_uid ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
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
                                @foreach ($days as $key => $daysa)
                                @if ($daysa == $day)
                                <option value="{{ $key }}" selected>{{ $daysa }}</option>
                                @else
                                <option value="{{ $key }}">{{ $daysa }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


				<div class="mt-5 mb-5 md:w-1/2 sm:w-full">

					<label for="gender" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Gender</label>
					<select name="gender" id="gender"
							class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
						<option>Gender</option>
						@foreach ($genders as $key => $gender)
						@if ($key == $registration->gender)
						<option value="{{ $key }}" selected>{{ $gender }}</option>
						@else
						<option value="{{ $key }}">{{ $gender }}</option>
						@endif
						@endforeach
					</select>


				</div>

                <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Special Dietary
                        Requirements</label>
					<input type="text" name="extra-info" id="extra-info"  value=" {{$registration->additional_information}}"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>


                </div>
                <hr class="h-1 bg-gray-200 border-0 dark:bg-gray-700">
                <fieldset class="mt-5 mb-5">
                    <legend class="text-xl font-semibold leading-6 text-gray-900">Carpool from Europe</legend>
                    <p class="mt-1 text-base leading-6 text-gray-600">If you plan to drive to Umeå and would like to share a ride,
                        please add your name to the carpool.</p>
                    <div class="mt-6 space-y-1">
                        <div class="flex items-center gap-x-3">
                            <input id="driver" name="productID" value="1009" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-gray-600" {{ $optionalsforregistration->contains(1009)==true ? 'checked': '' }}>
                            <label for="driver" class="block sm:text-base font-sm leading-6 text-gray-900">Driver looking for
                                passengers</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="vehicle" name="productID" value="1010" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-gray-600" {{ $optionalsforregistration->contains(1010)==true ? 'checked': '' }}>
                            <label for="vehicle" class="block sm:text-base font-sm leading-6 text-gray-900">Passenger looking for
                                vehicle</label>
                        </div>
                        <div class="flex items-center gap-x-3">
                            <input id="no-carpool" name="productID" value="0" type="radio"
                                   class="h-4 w-4 border-black text-black focus:ring-gray-600" {{$optionalsforregistration->count() == 0 ? 'checked': '' }}>
                            <label for="no-carpool" class="block sm:text-base font-sm leading-6 text-gray-900">No carpool</label>
                        </div>
                    </div>
                </fieldset>
                <hr class="h-1 bg-gray-200 border-0 dark:bg-gray-700">
                <fieldset class="mt-5 mb-5">
                    <legend class="text-xl font-semibold leading-6 text-gray-900">Included in the entry fee</legend>
                    <div class="mt-6 space-y-6">
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="pre_event_coffee_ride" name="1000" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"  @checked(old('1000', $optionalsforregistration->contains(1000)))
                                >
                            </div>
                            <div class="text-sm leading-6">
                                <label for="pre_event_coffee_ride" class="font-medium text-gray-900">Pre-event coffee ride - Scandic
                                    Plaza</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="lunch_box" name="1001" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"  @checked(old('1001', $optionalsforregistration->contains(1001)))>
                            </div>
                            <div class="text-sm leading-6">
                                <label for="lunch_box" class="font-medium sm:text-sm text-gray-900">Lunch box - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="bag_drop" name="1002" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" @checked(old('1002', $optionalsforregistration->contains(1002)))>
                            </div>
                            <div class="text-sm leading-6">
                                <label for="bag_drop" class="font-medium sm:text-sm text-gray-900">Bag drop - Baggböle Manor (to Scandic
                                    Plaza)</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="long_term_parking" name="1003" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600" @checked(old('1003', $optionalsforregistration->contains(1003)))>
                            </div>
                            <div class="text-sm leading-6">
                                <label for="long_term_parking" class="font-medium sm:text-sm text-gray-900">
                                    Long-term parking - Baggböle Manor</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="buffet_dinner" name="1004" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600" @checked(old('1004', $optionalsforregistration->contains(1004)))>
                            </div>
                            <div class="text-sm leading-6">
                                <label for="buffet_dinner" class="font-medium sm:text-sm text-gray-900">Buffet Dinner- Brännland
                                    Inn</label>
                            </div>
                        </div>
                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                                <input id="midsummer" name="1005" type="checkbox"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-blue-600" @checked(old('1004', $optionalsforregistration->contains(1005)))>
                            </div>
                            <div class="text-sm leading-6">
                                <label for="midsummer" class="font-medium sm:text-sm text-gray-900">Swedish Midsummer Celebration -
                                    Norrmjöle</label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr class="h-1 my-12 bg-gray-900 border-0 dark:bg-gray-700">
                <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                    <button type="submit" value="reserve" name="save"
                            class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
