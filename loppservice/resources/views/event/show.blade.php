<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<body>
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('cykelintresset.svg') }}"/>
	</div>
</header>
<div class="container mx-auto p-0 font-sans">
	<div class="mb-5">
		<p class="mb-3">Cykelintressets brevet-serie är registrerad hos Audax Club Parisien (ACP) och världsorganisationen Randonneurs Mondiaux (RM) som främjar randonnéecykling globalt.</p>
		<p>Samtliga brevet-lopp i serien har därför beteckningen ”BRM” (Brevets de Randonneurs Mondiaux). Brevet-serien består av fyra distanser: 200, 300, 400 och 600 km. </p>
		<p class="mb-3">	Distanserna kan slutföras i valfri ordning och kombineras med breveter arrangerade av andra randonneurklubbar globalt. ACP utfärdar en Super Randonneur-medalj till cyklister som slutför seriens fyra brevet-distanser. </p>
		<p class="mb-3">	Medaljen utdelas kostnadsfritt till Cykelintressets klubbmedlemmar samt till medaljtagare vars cykelklubb inte arrangerar ACP-sanktionerade brevet-lopp. Breveterna kan även genomföras som individuella lopp</p>
	</div>
	@foreach ($allevents as $key =>  $event)
	<div class="relative flex py-5 items-center">
		<div class="flex-grow border-t border-4 border-black"></div>
		<span class="flex-shrink mx-4 text-black  text-2xl font-semibold  flex-container-bold">{{$key}}</span>
		<div class="flex-grow border-t border-4  border-black"></div>
	</div>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-4">
		@foreach ($event as  $monthevent)
		<div class="max-w-lg rounded overflow-hidden shadow-lg">
			<div class="strava-embed-placeholder" data-embed-type="route" data-embed-id="{{$monthevent->embedid}}" data-units="metric" data-full-width="true" data-style="standard" data-from-embed="false"></div><script src="https://strava-embeds.com/embed.js"></script>
			<div class="h-12 ml-7 mt-2 mr-3">
				{{$monthevent->description}}
			</div>
			<div class="px-6 pt-4 pb-2">
				<button
					onclick="window.location='{{ route('register', $monthevent->event_uid)  . '?' . http_build_query(['event_type'=> $monthevent->event_type ]) }}'"
					class="w-full bg-black hover:bg-gray-500 text-white font-bold py-2 px-4 border text-bold border-black rounded">
					ANMÄL DIG HÄR
				</button>
				<button
					onclick="window.location='{{ url($monthevent->startlisturl)}}'"
					class="mt-3 w-full bg-black hover:bg-gray-500 text-white py-2 px-4 border text-bold border-black rounded">
					SE STARTLISTA
				</button>
			</div>
		</div>
		@endforeach
	</div>
	@endforeach
</div>
</body>
</html>
