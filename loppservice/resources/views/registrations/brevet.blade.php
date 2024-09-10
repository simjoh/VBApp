<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased">
<header class="bg-white py-4">
<!--	<div class="container sm:p-1 mx-auto">-->
<!--		<img alt="brm logotyp" width="75%" height="800" src="{{ asset('cykelintresset.svg') }}"/>-->
<!--	</div>-->
</header>
<div class="mx-auto p-0 font-sans">

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

<!--	<div class="mb-5">-->
<!--		<p class="mb-3">Startavgift per brevet: 100 kr (ej återbetalbar dock överförbar). ACP utfärdar distansmedaljer till respektive-->
<!--			distans.</p>-->
<!--		<p>Distansmedaljer utfärdas endast för officiella brevet-distanser och inte för brevet populaire-distanser under 200 km. Tillägg-->
<!--			för distansmedalj (inkluderar porto): 150 kr (återbetalas ej vid DNS/DNF).</p>-->
<!---->
<!--	</div>-->

	<form method="post" action="{{url('registration.create')}}" class="grid sm:grid-cols-1 gap-4">
		@csrf
		@method('POST')
		<input type="text" value="{{$event}}" hidden="hidden" id="uid"
			   name="uid">
		<!--		<hr class="h-1 my-4 bg-gray-900 border-t border-4 border-black dark:bg-gray-700">-->
		<div class="mt-2 flex-grow border-t border-4 border-black"></div>
		<div class="border-gray-900/10 pb-3">
			<div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
				<div>
					<label for="first-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Förnamn</label>
					<input type="text" id="first-name" name="first_name"
						   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
						   autocomplete="given-name" required>
				</div>
				<div>
					<label for="last-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Efternamn</label>
					<input type="text" id="last-name" name="last_name"
						   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
						   autocomplete="family-name" required>
				</div>
			</div>

			<div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
				<div class="mt-2">

					<label for="email" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Epost address*</label>
					<input id="email" name="email" type="email" autocomplete="email"
						   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
				</div>
				<div class="mt-2 mb-4">
					<label for="email-confirm" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Bekräfta
						epost adress*</label>
					<input id="email-confirm" name="email-confirm" type="email"
						   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
				</div>
			</div>

			<div class="mt-2 w-1/2">
				<label for="tel" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Telefon</label>
				<input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
					   class=" w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
			</div>

			<div class="mt-2">
				<label for="street-adress" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Adress</label>
				<input type="text" name="street-address" id="street-address" autocomplete="street-address"
					   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
			</div>

			<div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
				<div class="mt-2">
					<label for="postal-code" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Postnummer</label>
					<input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
						   class=" w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
				</div>
				<div class="mt-2 mb-4">
					<label for="city" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Ort</label>
					<input type="text" name="city" id="city" autocomplete="address-level2"
						   class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
				</div>
			</div>
		</div>

		<div class="mt-3">
			<label for="gender" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Kön</label>
			<select id="gender" name="gender" autocomplete="gender-name"
					class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600"
					required>
				<option>Välj kön</option>
				@foreach ($genders as $key => $gender)
				<option value="{{$key}}">
					{{$gender}}
				</option>
				@endforeach
			</select>
		</div>

		<div class="mt-3">
			<label for="country" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Land</label>
			<select id="country" name="country" autocomplete="country-name"
					class="sm:w-full px-3 py-2 md:w-1/2 lg:w-1/2 py-2 border-2 focus:outline-none focus:border-gray-600"
					required>
				<option>Välj land</option>
				@foreach ($countries as $country)
				<option value="{{$country->country_id}}">
					{{$country->country_name_sv}}
				</option>
				@endforeach
			</select>
		</div>

		<p class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Födelsedag</p>
		<div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
			<div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
				<div class="mt-2">

					<select name="year" id="year"
							class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
						<option>År</option>
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
				</div>
				<div class="mt-2">

					<select name="day" id="day"
							class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
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

		</div>
		<div class="mt-2 mb-4">
			<label for="club" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Klubb</label>
			<input type="text" name="club" id="club"
				   class="md:w-1/2  sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
		</div>

		<div class="mt-5 mb-5 md:w-1/2 sm:w-full">
			<label for="extra-info" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Övrig information</label>
			<p class="mt-1 mb-2 text-base leading-6 text-gray-600">Övrig information</p>
			<textarea id="extra-info" name="extra-info" rows="1"
					  class="sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"></textarea>
		</div>

		<fieldset class="mt-5">
			<p class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Medalj</p>
			<div class="mt-1 space-y-1 mb-6">
				<div class="flex items-center gap-x-3">
					<input id="medal" name="medal" value="1014" type="radio"
						   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
					<label for="buffe_dinner" class="block sm:text-base font-sm leading-6 text-gray-600">Förbetala distansmedalj,
						150:-</label>
				</div>
				<div class="flex items-center gap-x-3">
					<input id="no-medal" name="medal" value="nomedal" type="radio" checked
						   class="h-4 w-4 border-black text-black focus:ring-indigo-600">
					<label for="no-buffedinner" class="block sm:text-base font-sm leading-6 text-gray-600">Ingen distansmedalj</label>
				</div>
			</div>
		</fieldset>

		<button type="submit" value="{{$registrationproduct}}" name="save"
				class="w-full mt-3 w-full bg-black hover:bg-gray-500 text-white font-bold py-2 px-4 border text-bold border-black rounded">
			ANMÄL DIG
		</button>
	</form>
</div>
</body>
</html>