<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('base')
    <body class="antialiased">
      <div class="container mx-auto">
      @if ($errors->any())
      <div class="alert alert-danger">
      <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      </ul>
      </div>
      @endif
        <form method="post">
          @csrf

          <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Registration</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Use a permanent address where you can receive mail.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
              <div class="sm:col-span-3">
                <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">First name</label>
                <div class="mt-2">
                  <input type="text" name="first_name" id="first-name" autocomplete="given-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>

              <div class="sm:col-span-3">
                <label for="last-name" class="block text-sm font-medium leading-6 text-gray-900">Last name</label>
                <div class="mt-2">
                  <input type="text" name="last_name" id="last-name" value="{{ old('last_name') }}" autocomplete="family-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>

              <div class="sm:col-span-4">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                <div class="mt-2">
                  <input id="email" name="email" type="email" autocomplete="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>


              <div class="col-span-full">
                <label for="street-address" class="block text-sm font-medium leading-6 text-gray-900">Street address</label>
                <div class="mt-2">
                  <input type="text" name="street-address" id="street-address" autocomplete="street-address" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>

              <div class="sm:col-span-2">
                <label for="postal-code" class="block text-sm font-medium leading-6 text-gray-900">ZIP / Postal code</label>
                <div class="mt-2">
                  <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>

              <div class="sm:col-span-4">
                <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                <div class="mt-2">
                  <input type="text" name="city" id="city" autocomplete="address-level2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
              </div>

              <div class="sm:col-span-3">
                <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
                <div class="mt-2">
                    <select id="country" name="country" autocomplete="country-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                        <option>Select country</option>
                        @foreach ($countries as $country)
                        <option value="{{$country->country_id}}" >
                           {{$country->country_name_en}}
                        </option>
                        @endforeach
                    </select>
                </div>
              </div>

                <div class="col-span-full">
                    <label for="about" class="block text-sm font-medium leading-6 text-gray-900">Special Dietary Requirements</label>
                    <div class="mt-2">
                        <textarea id="extra-info"  name="extra-info" rows="5"  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                    </div>
<!--                    <p class="mt-3 text-sm leading-6 text-gray-600">Write a few sentences about yourself.</p>-->
                </div>

                <div class="mt-10 space-y-10 col-span-full">
                    <fieldset>
                        <legend class="text-sm font-semibold leading-6 text-gray-900">Included in the entry fee</legend>
                        <div class="mt-6 space-y-6">
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="pre_event_coffee_ride" name="pre_event_coffee_ride" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="pre_event_coffee_ride" class="font-medium text-gray-900">Pre-event coffee ride - Umeå Plaza, Saturday 15 June, 10:00.</label>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="lunch_box" name="lunch_box" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="lunch_box" class="font-medium text-gray-900">Lunch box - Baggböle Manor, Sunday 16 June, 15:00.</label>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="bag_drop" name="bag_drop" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="bag_drop" class="font-medium text-gray-900">Bag drop Umeå Plaza - Baggböle Manor, Sunday 16 June, 15:00.</label>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="long_term_parking" name="long_term_parking" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="long_term_parking" class="font-medium text-gray-900">Long-term parking - Baggböle Manor, Sunday 16 June - Thursday 20 June.</label>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="buffet_dinner" name="buffet_dinner" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="buffet_dinner" class="font-medium text-gray-900">Buffet Dinner- Brännland Inn, Sunday 16 June, 19:00.</label>
                                </div>
                            </div>
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input id="midsummer" name="midsummer" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="midsummer" class="font-medium text-gray-900">Swedish Midsummer Celebration - Friday 20 June, 12:00.</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>


                <div class="mt-10 space-y-10 col-span-full">
                    <fieldset>
                        <legend class="text-sm font-semibold leading-6 text-gray-900">Not included in the entry fee</legend>
                        <p class="mt-1 text-sm leading-6 text-gray-600">These are delivered via SMS to your mobile phone.</p>
                        <div class="mt-6 space-y-6">
                            <div class="flex items-center gap-x-3">
                                <input id="female-core" name="jersey" value="female-core" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="female-core" class="block text-sm font-medium leading-6 text-gray-900">MSR Jersey - Female, Core Fittet, 680 SEK (-25%)</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="female-tor" name="jersey" value="female-tor" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="female-tor" class="block text-sm font-medium leading-6 text-gray-900">MSR Jersey - Female, Tor, 980 SEK (-25%)</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="male-core" name="jersey" value="male-core" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="male-core" class="block text-sm font-medium leading-6 text-gray-900">MSR Jersey - Male, Core Fittet, 680 SEK (-25%)</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="male-tor" name="jersey" value="male-tor"  type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="male-tor" class="block text-sm font-medium leading-6 text-gray-900">MSR Jersey - Male, Tor, 980 SEK (-25%)</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-sm font-semibold leading-6 text-gray-900">Carpool from Europe</legend>
                        <p class="mt-1 text-sm leading-6 text-gray-600">These are delivered via SMS to your mobile phone.</p>
                        <div class="mt-6 space-y-6">
                            <div class="flex items-center gap-x-3">
                                <input id="driver" name="carpool" value="driver"  type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="driver" class="block text-sm font-medium leading-6 text-gray-900">Driver looking for passengers</label>
                            </div>
                            <div class="flex items-center gap-x-3">
                                <input id="vehicle" name="carpool"  value="vehicle" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="vehicle" class="block text-sm font-medium leading-6 text-gray-900">Passenger looking for vehicle</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
          </div>
<div class="mt-6 flex items-center justify-end gap-x-6">
    <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
    <button type="submit" value="reserve" name="save" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Reserve</button>
    <button type="submit" value="Registration" name="save" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Registration</button>
  </div>
          </form>
        </div>
  </body>
</html>
