<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('base')
</head>
<header class="bg-white py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2025.svg') }}" />
    </div>
</header>

<body class="antialiased bg-stone-100 w-full h-full">

    <div class="container mx-auto p-0 font-sans">
        <div class="bg-orange-50 p-4 shadow-md">


            <form method="post" action="{{url('registration.msrcomplete')}}" class="space-y-6">
                @csrf
                @method('POST')

                <input type="text" value="{{$availabledetails['event_uid']}}" hidden="hidden" id="uid" name="uid">
                <input type="text" value="{{$registration_uid ?? ''}}" hidden="hidden" name="registration_uid">



                <hr class="h-1 mb-4 mt-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mt-5 mb-2 md:w-full sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-semibold text-xl sm:leading-10">Special dietary
                        requirements</label>
                    <p class="mt-1 mb-2 text-base leading-6 text-gray-600">Please let us know if you will have any special requirements concerning the event's food menu, for instance allergies, gluten or lactose intolerance, vegan or vegetarian meals etc.</p>
                    <textarea id="extra-info" name="extra-info" rows="1"
                        class="sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"></textarea>
                </div>


                @include('registrations.partials.optional-products')




                <button id="checkout-button" type="submit" value="{{$registrationproduct}}" name="save"
                    class="w-full py-2 px-4 font-bold rounded-md focus:outline-none @if(isset($reservation_expired) && $reservation_expired) bg-gray-400 cursor-not-allowed @else bg-orange-500 hover:bg-orange-400 focus:bg-orange-600 @endif text-white"
                    @if(isset($reservation_expired) && $reservation_expired) disabled @endif>
                    @if(isset($reservation_expired) && $reservation_expired)
                    RESERVATION EXPIRED
                    @else
                    CHECK OUT
                    @endif
                </button>
            </form>

        </div>
    </div>
</body>


</html>
