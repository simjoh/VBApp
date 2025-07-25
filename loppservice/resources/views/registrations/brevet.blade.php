<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<header class="bg-[#aaaaaa] py-0">
    <div class="container mx-auto px-2 sm:px-4 max-w-7xl">
        <img alt="msr logotyp" class="mx-auto w-[200px] w-full" src="{{ asset('ebrevet-rando-anmalan.svg') }}"/>
    </div>
</header>
<body class="antialiased bg-[#aaaaaa]">
<div class="container mx-auto px-2 sm:px-2 lg:px-4 font-sans max-w-7xl">

    <!-- Information message with light gray background -->
    <div class="bg-[#dddddd] mb-6 p-6 mt-0.5">
        <!-- Event title and date -->

        <p class="text-gray-800 mb-2">Efter anmälan får du ett mail med inloggningsuppgifter till ditt digitala brevet-kort. OBS! Undersök mejlprogrammets inbox för skräppost om du inte fått ett mejl inom ett par minuter efter anmälan.</p>
    </div>

	@if ($errors->any())
	<div class="alert alert-danger">
		<ul>
			<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
				<strong class="font-bold">Something went wrong</strong>
				@foreach ($errors->all() as $error)
				<span class="block sm:inline"><li>{{ $error }}</li></span>
				@endforeach

				<span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.parentElement.style.display='none';">
    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
		 viewBox="0 0 20 20"><title>Close</title><path
			d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
  </span>
			</div>
		</ul>
	</div>
	@endif

	<p class="text-3xl font-bold text-[#f5e4a3] mb-4 mt-4"> {{ $availabledetails['event_name'] ?? '' }} {{ $availabledetails['startdate'] ?? '' }}</p>

	<div class="border-t-4 border-[#dddddd] my-6"></div>

	<form method="post" action="{{url('registration.create')}}" class="space-y-6">
		@csrf
		@method('POST')
		<input type="text" value="{{$availabledetails['event_uid']}}" hidden="hidden" id="uid"
			   name="uid">
		<div class="space-y-6">
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label for="first-name" class="block text-gray-900 font-semibold text-sm sm:text-base">Förnamn <span class="text-red-500">*</span></label>
					<input type="text" id="first-name" name="first_name"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
						   autocomplete="given-name" required>
				</div>
				<div>
					<label for="last-name" class="block text-gray-900 font-semibold text-sm sm:text-base">Efternamn <span class="text-red-500">*</span></label>
					<input type="text" id="last-name" name="last_name"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
						   autocomplete="family-name" required>
				</div>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label for="email" class="block text-gray-900 font-semibold text-sm sm:text-base">Epost address <span class="text-red-500">*</span></label>
					<input id="email" name="email" type="email" autocomplete="email"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
				<div>
					<label for="email-confirm" class="block text-gray-900 font-semibold text-sm sm:text-base">Bekräfta epost adress <span class="text-red-500">*</span></label>
					<input id="email-confirm" name="email-confirm" type="email"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label for="tel" class="block text-gray-900 font-semibold text-sm sm:text-base">Telefon <span class="text-red-500">*</span></label>
					<input type="text" name="tel" id="tel" autocomplete="tel"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
				<div>
					<label for="street-adress" class="block text-gray-900 font-semibold text-sm sm:text-base">Adress<span class="text-red-500">*</span></label>
					<input type="text" name="street-address" id="street-address" autocomplete="street-address"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<div>
					<label for="postal-code" class="block text-gray-900 font-semibold text-sm sm:text-base">Postnummer<span class="text-red-500">*</span></label>
					<input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
				<div>
					<label for="city" class="block text-gray-900 font-semibold text-sm sm:text-base">Ort<span class="text-red-500">*</span></label>
					<input type="text" name="city" id="city" autocomplete="address-level2"
						   class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
				</div>
			</div>

			<div>
				<label for="gender" class="block text-gray-900 font-semibold text-sm sm:text-base">Kön<span class="text-red-500">*</span></label>
				<select id="gender" name="gender" autocomplete="gender-name"
						class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
						required>
					<option>Välj kön</option>
					@foreach ($genders as $key => $gender)
					<option value="{{$key}}">{{$gender}}</option>
					@endforeach
				</select>
			</div>

			<div>
				<label for="country" class="block text-gray-900 font-semibold text-sm sm:text-base">Land<span class="text-red-500">*</span></label>
				<select id="country" required name="country" autocomplete="country-name"
						class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
						required>
					<option>Välj land</option>
					@foreach ($countries as $country)
					<option value="{{$country->country_id}}">{{$country->country_name_sv}}</option>
					@endforeach
				</select>
			</div>

			<div>
				<p class="block text-gray-900 font-semibold text-sm sm:text-base">Födelsedag<span class="text-red-500">*</span></p>
				<div class="grid grid-cols-3 gap-4 sm:w-1/2">
					<select name="year" id="year"
							class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
						<option>År</option>
						@foreach ($years as $year)
						<option value="{{$year}}">{{$year}}</option>
						@endforeach
					</select>
					<select name="month" id="month"
							class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
						<option value="">Månad</option>
						<option value="01">Januari</option>
						<option value="02">Februari</option>
						<option value="03">Mars</option>
						<option value="04">April</option>
						<option value="05">Maj</option>
						<option value="06">Juni</option>
						<option value="07">Juli</option>
						<option value="08">Augusti</option>
						<option value="09">September</option>
						<option value="10">Oktober</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="day" id="day"
							class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
						<option value="">Dag</option>
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

			<div>
				<label for="club" class="block text-gray-900 font-semibold text-sm sm:text-base">Klubb<span class="text-red-500">*</span></label>
				<select name="club_uid" id="club"
						class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
					<option value="">Välj klubb</option>
					@foreach ($clubs as $club)
					<option value="{{ $club->club_uid }}">
						{{ $club->name }} {{ $club->acp_code ? '('.$club->acp_code.')' : '' }}
					</option>
					@endforeach
				</select>
				<p class="text-sm text-gray-500 mt-1">För BRM-evenemang måste du välja en officiell klubb som erkänns av Audax Club Parisien</p>
				<p class="text-sm text-gray-500 mt-1">Välj "Independent Sweden" om din klubb inte finns i listan</p>
			</div>

			<div>
				<label for="extra-info" class="block text-gray-900 font-semibold text-sm sm:text-base">Övrig information</label>
				<p class="mt-1 text-sm text-gray-600">Om mat ingår i evenemanget ange information om specialkost, allergier mm.</p>
				<textarea id="extra-info" name="extra-info" rows="3" maxlength="100"
						  class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"></textarea>
			</div>

			@if ($availabledetails['event_type'] === 'BRM')
			<fieldset>
				<p class="block text-gray-900 font-semibold text-sm sm:text-base">Medalj</p>
				<div class="mt-4 space-y-4">
					<div class="flex items-center gap-x-3">
						<input id="medal" name="medal" value="1014" type="radio"
							   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
						<label for="medal" class="text-sm sm:text-base text-gray-600">Förbetala distansmedalj</label>
					</div>
					<div class="flex items-center gap-x-3">
						<input id="no-medal" name="medal" value="nomedal" type="radio" checked
							   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
						<label for="no-medal" class="text-sm sm:text-base text-gray-600">Ingen distansmedalj</label>
					</div>
				</div>
			</fieldset>
			@endif

			@if ($availabledetails['event_type'] === 'BRM' || $availabledetails['event_type'] === 'BP')
			<fieldset>
				<p class="block text-gray-900 font-semibold text-sm sm:text-base">Brevet-kort</p>
				<div class="mt-4 space-y-4">
					<div class="flex items-center gap-x-3">
						<input id="use_physical_brevet_card" name="use_physical_brevet_card" value="1" type="checkbox"
							   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
						<label for="use_physical_brevet_card" class="text-sm sm:text-base text-gray-600">Jag vill använda fysiskt brevet-kort istället för digitalt</label>
					</div>
				</div>
			</fieldset>
			@endif


			<div class="flex items-start">
				<div class="flex items-center h-5">
					<input type="checkbox" name="gdpr" id="gdpr" class="h-4 w-4 rounded border-gray-300" onchange="toggleSubmitButtons()">
				</div>
				<div class="ml-3">
					<label for="gdpr" class="text-sm sm:text-base text-gray-900">

					Jag ger Randonneurs Sverige tillstånd att lagra uppgifterna som jag lämnat i enlighet med dataskyddsförordningen <a href="https://www.ebrevet.org/datapolicy" target="_blank" class="text-black-500 underline">(GDPR), Regulation (EU) 2016/679)</a>
					</label>
				</div>
			</div>

			<!-- Horizontal line above the registration button -->
			<div class="border-t-4 border-[#dddddd] my-6"></div>

			<div>
				@if ($availabledetails['isRegistrationOpen'] == true)
				<button id="checkout-button" type="submit" value="{{$registrationproduct}}" name="save"
						class="w-full sm:w-auto px-6 py-3 bg-[#f5e4a3] text-[#3780b5] font-bold rounded-md hover:bg-[#f0dfa0] focus:outline-none focus:bg-[#e8d795] disabled:opacity-50" disabled>
					REGISTRERA
				</button>
				@else
				<button disabled type="submit" value="{{$registrationproduct}}" name="save"
						class="w-full sm:w-auto px-6 py-3 bg-[#f5e4a3] text-[#3780b5] font-bold rounded-md hover:bg-[#f0dfa0] focus:outline-none focus:bg-[#e8d795] disabled:opacity-50">
					REGISTRERING - ÖPPNAR {{ $availabledetails['opens']}}
				</button>
				@endif
			</div>
		</div>
	</form>
</div>

<script>
	function toggleSubmitButtons() {
		const gdprCheckbox = document.getElementById('gdpr');
		const checkoutButton = document.getElementById('checkout-button');
		checkoutButton.disabled = !gdprCheckbox.checked;
	}
</script>
</body>
</html>
