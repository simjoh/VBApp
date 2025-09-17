<BR>
<hr class="h-1 bg-gray-200 border-0 dark:bg-gray-700">
<fieldset class="mt-5 mb-5">
    <legend class="text-xl font-semibold leading-6 text-gray-900">Carpool from Europe</legend>
    <p class="mt-1 text-base leading-6 text-gray-900">If you plan to drive to Umeå and would like to share a ride,
        please add your name to the carpool.</p>
    <div class="mt-6 space-y-1">
        <div class="flex items-center gap-x-3">
            <input id="driver" name="productID" value="1009" type="radio"
                   class="h-4 w-4 border-black text-black focus:ring-gray-600">
            <label for="driver" class="block sm:text-base font-sm leading-6 text-gray-600">Driver looking for
                passengers</label>
        </div>
        <div class="flex items-center gap-x-3">
            <input id="vehicle" name="productID" value="1010" type="radio"
                   class="h-4 w-4 border-black text-black focus:ring-gray-600">
            <label for="vehicle" class="block sm:text-base font-sm leading-6 text-gray-600">Passenger looking for
                vehicle</label>
        </div>
        <div class="flex items-center gap-x-3">
            <input id="no-carpool" name="productID" value="no-carpool" type="radio" checked
                   class="h-4 w-4 border-black text-black focus:ring-gray-600">
            <label for="no-carpool" class="block sm:text-base font-sm leading-6 text-gray-600">No carpool</label>
        </div>
    </div>
</fieldset>
<hr class="h-1 my-8 bg-gray-200 border-0 dark:bg-gray-700">
<fieldset>
    <legend class="text-xl font-semibold leading-6 text-gray-900">Included in the entry fee</legend>
    <p class="mt-1 text-base leading-6 text-gray-900">Please look through the event program before you submit your
        choices.</p>
    <div class="mt-6 space-y-1">
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="pre_event_coffee_ride" name="1000" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            </div>
            <div class="text-sm leading-2">
                <label for="pre_event_coffee_ride" class="font-sm sm:text-base text-gray-600">Pre-event coffee ride -
                    Scandic Plaza</label>
            </div>
        </div>
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="lunch_box" name="1001" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            </div>
            <div class="text-sm leading-6">
                <label for="lunch_box" class="font-sm sm:text-base text-gray-600">Lunch - Brännland Inn</label>
            </div>
        </div>
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="bag_drop" name="1002" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            </div>
            <div class="text-sm leading-6">
                <label for="bag_drop" class="font-sm sm:text-base text-gray-600">Bag drop - Brännland Inn (to Scandic
                    Plaza)</label>
            </div>
        </div>
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="long_term_parking" name="1003" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
            </div>
            <div class="text-sm leading-6">
                <label for="long_term_parking" class="font-sm sm:text-base text-gray-600">
                    Long-term parking - Brännland Inn</label>
            </div>
        </div>
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="buffet_dinner" name="1004" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
            </div>
            <div class="text-sm leading-6">
                <label for="buffet_dinner" class="font-sm sm:text-base text-gray-600">Pre-ride meal - Brännland
                    Inn</label>
            </div>
        </div>
        <div class="relative flex gap-x-3">
            <div class="flex h-6 items-center">
                <input id="midsummer" name="1005" type="checkbox"
                       class="h-4 w-4 border-black text-black focus:ring-gray-600">
            </div>
            <div class="text-sm leading-6">
                <label for="midsummer" class="font-sm sm:text-base text-gray-600">Swedish Midsummer celebration -
                    Norrmjöle</label>
            </div>
        </div>
    </div>
</fieldset>
<hr class="h-1 my-8 bg-gray-200 border-0 dark:bg-gray-700">
<fieldset class="mt-5">
    <legend class="text-xl font-semibold leading-6 text-gray-900">Not included in the entry fee</legend>
    <p class="mt-1 text-base leading-6 text-gray-900">Purchase a digital voucher for the MSR jersey at a 20% discount.</p>
    <div class="mt-6 space-y-1 mb-6">
        <div class="flex items-center gap-x-3">
            <input id="malefemale-tor" name="jersey" value="1008" type="radio"
                   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            <label for="malefemale-tor" class="block sm:text-base font-sm leading-6 text-gray-600">TOR 3.0 jersey F/M
                (107 EUR in webshop -20%): 86 EUR</label>
        </div>
        <div class="flex items-center gap-x-3">
            <input id="malefemale-grand" name="jersey" value="1007" type="radio"
                   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            <label for="malefemale-grand" class="block sm:text-base font-sm leading-6 text-gray-600">GRAND jersey F/M
                (87 EUR in webshop -20%): 70 EUR</label>
        </div>
        <div class="flex items-center gap-x-3">
            <input id="no-jersey" name="jersey" value="nojersey" type="radio" checked
                   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
            <label for="no-jersey" class="block sm:text-base font-sm leading-6 text-gray-600">No jersey</label>
        </div>
    </div>

    <p class="mt-1 text-base leading-6 text-gray-900 mb-6">Please join us on Saturday the 14th of June, the day before the event, for a pre-booked buffet dinner at Brännland Inn which starts at 17:00. The dinner provides an excellent opportunity to meet participants and make new friends before the event starts.
        At the dinner you can also pick up and try on your pre-ordered MSR-jersey.</p>

    <div class="flex items-center gap-x-3">
        <input id="buffet_dinner_paid" name="dinner" value="1006" type="radio"
               class="h-4 w-4 border-black text-black focus:ring-indigo-600">
        <label for="buffet_dinner_paid" class="block sm:text-base font-sm leading-6 text-gray-600">Buffet Dinner - Saturday the 14th June: 38 EUR</label>
    </div>
    <div class="flex items-center gap-x-3">
        <input id="no-buffet-dinner" name="dinner" value="nobuffetdinner" type="radio" checked
               class="h-4 w-4 border-black text-black focus:ring-indigo-600">
        <label for="no-buffet-dinner" class="block sm:text-base font-sm leading-6 text-gray-600">No Buffet Dinner</label>
    </div>
</fieldset>
<hr class="h-1 my-5 bg-gray-900 border-0 dark:bg-gray-700">
