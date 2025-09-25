<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('base')
</head>
<body class="antialiased bg-stone-100 w-full h-full">
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2026.svg') }}"/>
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

                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.parentElement.style.display='none';" style="cursor: pointer;">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20"><title>Close</title><path
                             d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            </ul>
        </div>
        @endif

        <!-- Debug output -->
<!--         <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;">
            <strong>Debug Info:</strong><br>
            Event UID: {{ $availabledetails['event_uid'] ?? 'NOT SET' }}<br>
            Event Name: {{ $availabledetails['event_name'] ?? 'NOT SET' }}<br>
            Event Type: {{ $availabledetails['event_type'] ?? 'NOT SET' }}<br>
            Reservation Product: {{ $reservationproduct ?? 'NOT SET' }}<br>
            Full availabledetails: {{ print_r($availabledetails, true) }}
        </div> -->

        <form method="post" action="{{url('registration.msrreserve')}}" class="space-y-6">
            @csrf
            @method('POST')

            <input type="text" value="{{$availabledetails['event_uid']}}" hidden="hidden" id="uid" name="uid">

            <div class="border-gray-900/10 pb-3">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">First name <span class="text-red-500">*</span></label>
                        <input type="text" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Last name <span class="text-red-500">*</span></label>
                        <input type="text" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="email" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="email-confirm" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email confirmation <span class="text-red-500">*</span></label>
                        <input id="email-confirm" name="email-confirm" type="email"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="tel" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Mobile number <span class="text-red-500">*</span></label>
                    <input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-adress" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Street address <span class="text-red-500">*</span></label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Postal code <span class="text-red-500">*</span></label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" id="city" autocomplete="address-level2"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="country" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Country <span class="text-red-500">*</span></label>
                    <select id="country" name="country" autocomplete="country-name"
                            class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600"
                            required>
                        <option value="">Select country</option>
                        @foreach ($countries as $country)
                        <option value="{{$country->country_id}}">
                            {{$country->country_name_en}}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3 grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <p class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Birthdate <span class="text-red-500">*</span></p>
                    <p class="text-gray-900 font-semibold sm:text-base sm:leading-10">Gender <span class="text-red-500">*</span></p>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
                        <div class="mt-1">
                            <select name="year" id="year" class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                                <option value="">Year</option>
                                @foreach ($years as $year)
                                <option value="{{$year}}">{{$year}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-1">
                            <select name="month" id="month" class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                                <option value="">Month</option>
                                <option value="01">January</option>
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
                        <div class="mt-1">
                            <select name="day" id="day" class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
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

                    <div class="mt-2">
                        <select id="gender" name="gender" autocomplete="gender-name" class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                            <option value="">Select gender</option>
                            @foreach ($genders as $key => $gender)
                            <option value="{{$key}}">{{$gender}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-2 mb-4">
                    <label for="club" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Club <span class="text-red-500">*</span></label>
                    <input type="text" name="club" id="club"
                           class="md:w-1/2 sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <!-- GDPR Consent -->
                <div class="mt-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="gdpr-consent" name="gdpr_consent" type="checkbox"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                   required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="gdpr-consent" class="font-medium text-gray-900">
                                 <span class="text-red-500">*</span>
                                 I consent to the processing of my personal data for the purpose of event registration <a href="https://www.ebrevet.org/datapolicy" target="_blank" class="text-black-500 underline">(GDPR), Regulation (EU) 2016/679)</a>
                            </label>
                            <p class="text-gray-500">By checking this box, you agree to our privacy policy and data processing terms.</p>
                        </div>
                    </div>
                </div>

                <button id="reserve-button" type="submit" value="{{$reservationproduct}}" name="save"
                        data-registration-open="{{ $availabledetails['isRegistrationOpen'] ? 'true' : 'false' }}"
                        data-reservation-active="{{ $showreservationbutton ? 'true' : 'false' }}"
                        class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    RESERVE STARTING PLACE
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const gdprCheckbox = document.getElementById('gdpr-consent');
    const reserveButton = document.getElementById('reserve-button');
    const emailInput = document.getElementById('email');
    const emailConfirmInput = document.getElementById('email-confirm');

        // Check if registration and reservation are open
    const isRegistrationOpen = reserveButton.dataset.registrationOpen === 'true';
    const reservationActive = reserveButton.dataset.reservationActive === 'true';

    // Function to update button state
    function updateButtonState() {
        const gdprAccepted = gdprCheckbox.checked;

        if (!isRegistrationOpen || !reservationActive) {
            reserveButton.disabled = true;
            reserveButton.classList.add('opacity-50', 'cursor-not-allowed');

            if (!isRegistrationOpen) {
                reserveButton.textContent = 'Registration Opens {{ $availabledetails["opens"] ?? "TBD" }}';
                reserveButton.title = 'Registration period is not open for this event. Registration opens: {{ $availabledetails["opens"] ?? "TBD" }}';
            } else if (!reservationActive) {
                reserveButton.textContent = 'Reservations Not Available';
                reserveButton.title = 'Reservation period is not active for this event. Reservations are not currently available.';
            }
        } else if (!gdprAccepted) {
            reserveButton.disabled = true;
            reserveButton.classList.add('opacity-50', 'cursor-not-allowed');
            reserveButton.textContent = 'Agree to Terms to Continue';
            reserveButton.title = 'You must agree to the data processing terms to continue with your reservation';
        } else {
            reserveButton.disabled = false;
            reserveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            reserveButton.textContent = 'CHECK OUT';
            reserveButton.title = 'Submit your reservation';
        }
    }

    // Initial button state
    updateButtonState();

    // Update button when GDPR checkbox changes
    gdprCheckbox.addEventListener('change', updateButtonState);

    // Email confirmation validation
    function validateEmailConfirmation() {
        if (emailInput.value !== emailConfirmInput.value) {
            emailConfirmInput.setCustomValidity('Email addresses do not match');
        } else {
            emailConfirmInput.setCustomValidity('');
        }
    }

    emailInput.addEventListener('input', validateEmailConfirmation);
    emailConfirmInput.addEventListener('input', validateEmailConfirmation);

    // Form submission with GDPR validation
    form.addEventListener('submit', function(e) {
        // Prevent submission if button is disabled
        if (reserveButton.disabled) {
            e.preventDefault();
            return false;
        }

        if (!gdprCheckbox.checked) {
            e.preventDefault();
            alert('You must consent to data processing to continue.');
            return false;
        }

        // Change button text to show processing (but don't disable)
        reserveButton.textContent = 'Processing...';
    });

    // Real-time form validation
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });
    });
});
</script>
</body>
</html>
