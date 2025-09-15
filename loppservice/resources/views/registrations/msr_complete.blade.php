<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('base')
</head>
<body class="antialiased bg-stone-100 w-full h-full">
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2025.svg') }}"/>
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

<!--             @php
                // Parse birthdate for the form dropdowns
                $birthdate = $person->birthdate ?? '';
                $birth_year = '';
                $birth_month = '';
                $birth_day = '';

                if ($birthdate) {
                    $birthdate_parts = explode('-', $birthdate);
                    if (count($birthdate_parts) == 3) {
                        $birth_year = $birthdate_parts[0];
                        $birth_month = ltrim($birthdate_parts[1], '0'); // Remove leading zero
                        $birth_day = ltrim($birthdate_parts[2], '0'); // Remove leading zero
                    }
                }

                // Get club name from registration
                $club_name = '';
                if (isset($registration) && $registration->club_uid) {
                    $club = \App\Models\Club::where('club_uid', $registration->club_uid)->first();
                    $club_name = $club ? $club->name : '';
                }
            @endphp -->

            <form method="post" action="{{url('registration.msrcomplete')}}" class="space-y-6">
                @csrf
                @method('POST')

                <input type="text" value="{{$availabledetails['event_uid']}}" hidden="hidden" id="uid" name="uid">
                <input type="text" value="{{$registration_uid ?? ''}}" hidden="hidden" name="registration_uid">

                <div class="border-gray-900/10 pb-3">
                    <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                        <div>
                            <label for="first-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">First name <span class="text-red-500">*</span></label>
                            <input type="text" id="first-name" name="first_name"
                                   value="{{ old('first_name', $person->firstname ?? '') }}"
                                   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                                   autocomplete="given-name" required>
                        </div>
                        <div>
                            <label for="last-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Last name <span class="text-red-500">*</span></label>
                            <input type="text" id="last-name" name="last_name"
                                   value="{{ old('last_name', $person->surname ?? '') }}"
                                   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                                   autocomplete="family-name" required>
                        </div>
                    </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">

                        <label for="email" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email address <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email"
                               value="{{ old('email', $person->contactinformation->email ?? '') }}"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="email-confirm" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email
                            confirmation <span class="text-red-500">*</span></label>
                        <input id="email-confirm" name="email-confirm" type="email"
                               value="{{ old('email-confirm', $person->contactinformation->email ?? '') }}"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="mt-2 w-1/2">
                    <label for="tel" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Mobile number <span class="text-red-500">*</span></label>
                    <input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
                           value="{{ old('tel', $person->contactinformation->tel ?? '') }}"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="mt-2">
                    <label for="street-adress" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Street address <span class="text-red-500">*</span></label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                           value="{{ old('street-address', $person->adress->adress ?? '') }}"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                    <div class="mt-2">
                        <label for="postal-code" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Postal code <span class="text-red-500">*</span></label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                               value="{{ old('postal-code', $person->adress->postal_code ?? '') }}"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div class="mt-2 mb-4">
                        <label for="city" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">City <span class="text-red-500">*</span></label>
                        <input type="text" name="city" id="city" autocomplete="address-level2"
                               value="{{ old('city', $person->adress->city ?? '') }}"
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
                        <option value="{{$country->country_id}}"
                                @if(old('country', $person->adress->country_id ?? '') == $country->country_id) selected @endif>
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
								<option value="{{$year}}" @if(old('year', $birth_year) == $year) selected @endif>{{$year}}</option>
								@endforeach
							</select>
						</div>
						<div class="mt-1">
							<select name="month" id="month" class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
								<option value="">Month</option>
								<option value="01" @if(old('month', $birth_month) == '1') selected @endif>January</option>
								<option value="02" @if(old('month', $birth_month) == '2') selected @endif>February</option>
								<option value="03" @if(old('month', $birth_month) == '3') selected @endif>March</option>
								<option value="04" @if(old('month', $birth_month) == '4') selected @endif>April</option>
								<option value="05" @if(old('month', $birth_month) == '5') selected @endif>May</option>
								<option value="06" @if(old('month', $birth_month) == '6') selected @endif>June</option>
								<option value="07" @if(old('month', $birth_month) == '7') selected @endif>July</option>
								<option value="08" @if(old('month', $birth_month) == '8') selected @endif>August</option>
								<option value="09" @if(old('month', $birth_month) == '9') selected @endif>September</option>
								<option value="10" @if(old('month', $birth_month) == '10') selected @endif>October</option>
								<option value="11" @if(old('month', $birth_month) == '11') selected @endif>November</option>
								<option value="12" @if(old('month', $birth_month) == '12') selected @endif>December</option>
							</select>
						</div>
						<div class="mt-1">
							<select name="day" id="day" class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
								<option value="">Day</option>
								@for($d = 1; $d <= 31; $d++)
								<option value="{{ sprintf('%02d', $d) }}" @if(old('day', $birth_day) == $d) selected @endif>{{ sprintf('%02d', $d) }}</option>
								@endfor
							</select>
						</div>
					</div>

					<div class="mt-2">
						<select id="gender" name="gender" autocomplete="gender-name" class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600" required>
							<option value="">Select gender</option>
							@foreach ($genders as $key => $gender)
							<option value="{{$key}}" @if(old('gender', $person->gender ?? '') == $key) selected @endif>{{$gender}}</option>
							@endforeach
						</select>
					</div>
				</div>



                <div class="mt-2 mb-4">
                    <label for="club" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Club <span class="text-red-500">*</span></label>
                    <input type="text" name="club" id="club"
                           value="{{ old('club', $club_name) }}"
                           class="md:w-1/2 sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>

                <hr class="h-1 mb-4 mt-8 bg-gray-200 border-0 dark:bg-gray-700">

                <div class="mt-5 mb-2 md:w-full sm:w-full">
                    <label for="extra-info" class="block text-gray-900 font-semibold text-xl sm:leading-10">Special dietary
                        requirements</label>
                    <p class="mt-1 mb-2 text-base leading-6 text-gray-600">Please let us know if you will have any special requirements concerning the event's food menu, for instance allergies, gluten or lactose intolerance, vegan or vegetarian meals etc.</p>
                    <textarea id="extra-info" name="extra-info" rows="1"
                        class="sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600">{{ old('extra-info', $registration->additional_information ?? '') }}</textarea>
                </div>


                @include('registrations.partials.optional-products')

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
                                I consent to the processing of my personal data for the purpose of event registration <span class="text-red-500">*</span>
                            </label>
                            <p class="text-gray-500">By checking this box, you agree to our privacy policy and data processing terms.</p>
                        </div>
                    </div>
                </div>

                <button id="checkout-button" type="submit" value="{{$registrationproduct}}" name="save"
                    class="w-full py-2 px-4 font-bold rounded-md focus:outline-none @if(isset($reservation_expired) && $reservation_expired) bg-gray-400 cursor-not-allowed @else bg-orange-500 hover:bg-orange-400 focus:bg-orange-600 @endif text-white"
                    @if(isset($reservation_expired) && $reservation_expired) disabled @endif>
                    @if(isset($reservation_expired) && $reservation_expired)
                    RESERVATION EXPIRED
                    @else
                    CHECK OUT
                    @endif
                </button>
            </div>
        </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const gdprCheckbox = document.getElementById('gdpr-consent');
    const checkoutButton = document.getElementById('checkout-button');
    const emailInput = document.getElementById('email');
    const emailConfirmInput = document.getElementById('email-confirm');

    // Function to update button state
    function updateButtonState() {
        const gdprAccepted = gdprCheckbox.checked;
        // Check if reservation is expired from server data
        const isExpired = {{ isset($reservation_expired) && $reservation_expired ? 'true' : 'false' }};

        if (isExpired) {
            // If reservation is expired, keep the disabled state
            checkoutButton.disabled = true;
            checkoutButton.classList.add('opacity-50', 'cursor-not-allowed');
            checkoutButton.textContent = 'RESERVATION EXPIRED';
            checkoutButton.title = 'Your reservation has expired';
            return;
        }

        if (!gdprAccepted) {
            checkoutButton.disabled = true;
            checkoutButton.classList.add('opacity-50', 'cursor-not-allowed');
            checkoutButton.textContent = 'Agree to Terms to Continue';
            checkoutButton.title = 'You must agree to the data processing terms to continue with your checkout';
        } else {
            checkoutButton.disabled = false;
            checkoutButton.classList.remove('opacity-50', 'cursor-not-allowed');
            checkoutButton.textContent = 'CHECK OUT';
            checkoutButton.title = 'Submit your registration';
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
        if (checkoutButton.disabled) {
            e.preventDefault();
            return false;
        }

        if (!gdprCheckbox.checked) {
            e.preventDefault();
            alert('You must consent to data processing to continue.');
            return false;
        }

        // Change button text to show processing (but don't disable)
        checkoutButton.textContent = 'Processing...';
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
