<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<body>
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" class="mx-auto w-1/2 sm:w-1/2 md:w-[600px] max-w-[600px]" src="{{ asset('ebrevet-kalender.svg') }}"/>
	</div>
</header>
<div class="container mx-auto px-2 sm:px-4 font-sans max-w-7xl">
	<div class="p-4 sm:p-6">
		<p class="mb-3">Cykelintressets brevet-serie består av distanserna: 200, 300, 400 och 600 km och pågår mellan 11 maj och 13 juli</p>
		<p>Distanserna kan slutföras i valfri ordning och genomföras som individuella lopp. Brevet populaire-lopp på 100 km arrangeras tillsammans med 200 km-breveterna</p>
	</div>

	<div class="space-y-12">
		@foreach($allevents as $month => $events)
			<div class="month-section flex flex-col items-center">
				<div class="flex items-center justify-center mb-8 relative w-full max-w-[95%] sm:max-w-full">
					<div class="absolute w-full border-t-2 border-gray-600"></div>
					<h2 class="text-xl md:text-2xl font-bold text-gray-800 bg-white px-6 relative">
						{{ $month }}
					</h2>
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-3 w-full place-items-center">
					@foreach($events as $event)
						<div class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center h-auto min-h-[520px] w-[95%] sm:w-full sm:max-w-xs">
							<!-- Placeholder for SVG logo -->
							<div class="w-40 h-40 mb-3 flex items-center justify-center">
								@if($event->organizer && $event->organizer->logo_svg)
									<div class="w-full h-full">
										<svg viewBox="0 0 57.4999 57.4999" xmlns="http://www.w3.org/2000/svg"
											 class="w-full h-full">
											@php
												$svgContent = preg_replace('/<\?xml.*?\?>/', '', $event->organizer->logo_svg);
												$svgContent = preg_replace('/<svg[^>]*>/', '', $svgContent);
												$svgContent = str_replace('</svg>', '', $svgContent);
											@endphp
											{!! $svgContent !!}
										</svg>
									</div>
								@else
									<svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
									</svg>
								@endif
							</div>

							<div class="flex-1 w-full mb-3">
								<h2 class="text-base font-bold mb-1">{{ $event->title }}</h2>

								<div class="w-full space-y-0">
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Distans:</span>
										<span>{{ $event->distance ?? '200' }} KM</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Höjdmeter:</span>
										<span>{{ $event->elevation ?? '1310' }} M</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startdatum:</span>
										<span>{{ \Carbon\Carbon::parse($event->startdate)->format('d M') }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Starttid:</span>
										<span>07:00</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Sista anmälan:</span>
										<span>10 maj</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startort:</span>
										<span>Umeå</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Arrangör:</span>
										<span>{{ $event->organizer ? $event->organizer->organization_name : 'Arrangör ej angiven' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Övrigt:</span>
										<span>Cykelintresset brevet-serie</span>
									</div>
								</div>
							</div>

							<div class="w-full space-y-1 mt-auto">
								<a href="#" class="block w-full text-[#E4432D] hover:text-[#B32D1B] hover:underline text-sm flex items-center">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
									</svg>
									Länk till bana
								</a>
								<a href="{{ $event->startlisturl }}" class="block w-full text-[#E4432D] hover:text-[#B32D1B] hover:underline text-sm flex items-center">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
									</svg>
									Startlista
								</a>
								<a href="{{ route('register', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) }}"
								   class="block w-full text-[#E4432D] hover:text-[#B32D1B] hover:underline text-sm flex items-center">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
									</svg>
									Länk till anmälan och betalning
								</a>
								<a href="{{ route('event.login', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) }}"
								   class="block w-full text-center bg-[#666666] hover:bg-[#4D4D4D] text-white py-1 rounded text-sm flex items-center justify-center">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
									</svg>
									HÄMTA LOGIN
								</a>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		@endforeach
	</div>
</div>
</body>
</html>
