<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<body class="antialiased bg-gray-50">
<header class="bg-white py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" class="mx-auto" width="200" src="{{ asset('ebrevet-kalender.svg') }}"/>
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
					<div class="absolute w-full bg-[#f3ea4d] flex items-center justify-center">
						<span class="text-lg md:text-xl font-bold text-black px-10 py-0.5">{{ $month }}</span>
					</div>
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-3 w-full place-items-center mt-3">
					@foreach($events as $event)
						<div class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center h-full min-h-[520px] w-[95%] sm:w-full sm:max-w-xs">
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
										<span>{{ $event->routeDetail->distance ?? '200' }} KM</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Höjdmeter:</span>
										<span>{{ $event->routeDetail->height_difference ? $event->routeDetail->height_difference . ' M' : '' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startdatum:</span>
										<span>{{ $event->formatted_start_date }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Starttid:</span>
										<span>{{ $event->routeDetail->start_time ?? '07:00' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Sista anmälan:</span>
										<span>{{ $event->formatted_closing_date }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startort:</span>
										<span>{{ $event->routeDetail->start_place ?? '' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Arrangör:</span>
										<span>{{ $event->organizer ? $event->organizer->organization_name : 'Arrangör ej angiven' }}</span>
									</div>
									<div class="flex items-baseline text-sm">

										<span>
											@if($event->organizer && $event->organizer->website)
												<a href="{{ $event->organizer->website }}" target="_blank" class="text-blue-500 hover:underline">Hemsida</a>
											@else
												Hemsida ej angiven
											@endif
										</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Övrigt:</span>
										<span>
											@if($event->routeDetail->description ?? false)
												{{ $event->routeDetail->description }}
											@endif
										</span>
									</div>
								</div>
							</div>

							<div class="w-full space-y-1 mt-auto">
								@if($event->routeDetail && $event->routeDetail->track_link)
									<a href="{{ $event->routeDetail->track_link }}" target="_blank" class="block w-full text-[#0081b9] hover:text-[#B32D1B] hover:underline text-sm flex items-center">
										Länk till bana
									</a>
								@else
									<span class="block w-full text-gray-400 text-sm flex items-center cursor-not-allowed">
										Länk till bana
									</span>
								@endif
								<a href="{{ $event->startlisturl }}" class="block w-full text-[#0081b9] hover:text-[#B32D1B] hover:underline text-sm flex items-center">
									Startlista
								</a>
								@if(isset($event->eventConfiguration) && isset($event->eventConfiguration->use_stripe_payment) && $event->eventConfiguration->use_stripe_payment)
									<a href="{{ route('register', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) }}"
									   class="block w-full text-center bg-[#666666] hover:bg-[#4D4D4D] text-white py-2 rounded text-lg font-bold uppercase flex items-center justify-center mb-1">
										ANMÄLAN & BETALNING
									</a>
								@else
									<a href="{{ route('register', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) }}"
									   class="block w-full text-center bg-[#666666] hover:bg-[#4D4D4D] text-white py-2 rounded text-lg font-bold uppercase flex items-center justify-center">
										HÄMTA LOGIN
									</a>
								@endif
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
