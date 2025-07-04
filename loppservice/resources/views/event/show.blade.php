<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<header class="bg-[#aaaaaa] py-0">
    <div class="container mx-auto px-2 sm:px-4 max-w-7xl">
        <img alt="msr logotyp" class="mx-auto w-[200px] w-full" src="{{ asset('ebrevet-rando-kalender.svg') }}"/>
	</div>
</header>
<body class="antialiased  bg-[#aaaaaa]">

<div class="container mx-auto px-2 sm:px-2 lg:px-4 font-sans max-w-7xl">
	<div class="bg-[#dddddd] mb-6 p-6 shadow-sm mt-0.5">
		<p class="text-gray-800 mb-3">Hämta inloggningsuppgifter till ditt digitala brevet-kort genom att klicka på knappen "Hämta login". Efter att du registrerat dig får du dina inloggningsuppgifter i ett mail.
		OBS! Om du anmäler dig till Randonneurs Laponias och Cykelintressets lopp får du dina inloggningsuppgifter när du anmäler dig och betalat startavgiften.
		</p>
	</div>

	<div class="space-y-12">
		@foreach($allevents as $month => $events)
			<div class="month-section flex flex-col items-center">
				<div class="flex items-center justify-center mb-8 relative w-full max-w-[95%] sm:max-w-full mt-4">
					<div class="absolute w-full bg-[#f5e4a3] flex items-center justify-center">
						<span class="text-lg md:text-xl font-bold text-black px-10 py-0.5">{{ $month }}</span>
					</div>
				</div>

				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-4 w-full place-items-center mt-3">
					@foreach($events as $event)
						<div class="bg-white rounded-lg shadow-md p-4 flex flex-col items-center h-full min-h-[520px] w-full max-w-[95%] sm:max-w-xs mx-auto">
							<!-- Placeholder for SVG logo -->
							<div class="w-40 h-40 mb-3 flex items-center justify-center">
								@if($event->organizer && $event->organizer->logo_svg)
									<div class="w-full h-full">


									<img width="200" height="200" src="data:image/svg+xml;base64,{{ base64_encode($event->organizer->logo_svg) }}" alt="SVG Image">


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
										<span>{{ isset($event->routeDetail) && $event->routeDetail->distance ? $event->routeDetail->distance . ' KM' : 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Höjdmeter:</span>
										<span>{{ isset($event->routeDetail) && $event->routeDetail->height_difference ? $event->routeDetail->height_difference . ' M' : 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startdatum:</span>
										<span>{{ $event->formatted_start_date ?? 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Starttid:</span>
										<span>{{ isset($event->routeDetail) && $event->routeDetail->start_time ? $event->routeDetail->start_time : 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Sista anmälan:</span>
										<span>{{ $event->formatted_closing_date ?? 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Startort:</span>
										<span>{{ isset($event->routeDetail) && $event->routeDetail->start_place ? $event->routeDetail->start_place : 'N/A' }}</span>
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Arrangör:</span>
										<span>
											@if($event->organizer)
												@if($event->organizer->website)
													<a href="{{ $event->organizer->website }}" target="_blank" class="text-blue-500 hover:underline">{{ $event->organizer->organization_name }}</a>
												@else
													{{ $event->organizer->organization_name }}
												@endif
											@else
												Arrangör ej angiven
											@endif
										</span>
									</div>
									<div class="flex items-baseline text-sm">
										@if(isset($event->routeDetail) && $event->routeDetail->pay_link)
											<span class="font-semibold mr-1">Betala&nbsp;via:</span>
											<span>
												{{ $event->routeDetail->pay_link }}
											</span>
										@endif
									</div>
									<div class="flex items-baseline text-sm">
										<span class="font-semibold mr-1">Övrigt:</span>
										<span>{{ isset($event->routeDetail) && $event->routeDetail->description ? $event->routeDetail->description : '' }}</span>
									</div>
								</div>
							</div>

							<div class="w-full space-y-1 mt-auto">
								@if(isset($event->routeDetail) && $event->routeDetail->track_link)
									<a href="{{ $event->routeDetail->track_link }}" target="_blank" class="block w-full text-blue-500 hover:text-[#B32D1B] hover:underline text-sm flex items-center">
										Länk till bana
									</a>
								@else
									<span class="block w-full text-gray-400 text-sm flex items-center cursor-not-allowed">
										Länk till bana (ej tillgänglig)
									</span>
								@endif
								<a href="{{ $event->startlisturl ?? '#' }}" class="block w-full text-blue-500 hover:text-[#B32D1B] hover:underline text-sm flex items-center">
									Startlista
								</a>
								@if(isset($event->eventConfiguration?->use_stripe_payment) && $event->eventConfiguration->use_stripe_payment)
									@php
										$now = \Carbon\Carbon::now();
										$regOpens = isset($event->eventConfiguration->registration_opens) ? \Carbon\Carbon::parse($event->eventConfiguration->registration_opens) : null;
										$regCloses = isset($event->eventConfiguration->registration_closes) ? \Carbon\Carbon::parse($event->eventConfiguration->registration_closes) : null;
										$isRegistrationOpen = $regOpens && $now->gte($regOpens);
										$isRegistrationClosed = $regCloses && $now->gt($regCloses);
										$regActive = $isRegistrationOpen && !$isRegistrationClosed;
									@endphp
									<a {{ $regActive ? 'href=' . route('register', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) : '' }}
									   class="block w-full text-center {{ $regActive ? 'bg-[#666666] hover:bg-[#4D4D4D]' : 'bg-[#bbbbbb] cursor-not-allowed' }} text-white py-2 rounded text-lg font-bold uppercase flex items-center justify-center mb-1">
										{{ $isRegistrationClosed ? 'ANMÄLAN STÄNGD' : 'ANMÄLAN & BETALNING' }}
									</a>
								@else
									@php
										$now = \Carbon\Carbon::now();
										$regOpens = isset($event->eventConfiguration->registration_opens) ? \Carbon\Carbon::parse($event->eventConfiguration->registration_opens) : null;
										$regCloses = isset($event->eventConfiguration->registration_closes) ? \Carbon\Carbon::parse($event->eventConfiguration->registration_closes) : null;
										$isRegistrationOpen = $regOpens && $now->gte($regOpens);
										$isRegistrationClosed = $regCloses && $now->gt($regCloses);
										$regActive = $isRegistrationOpen && !$isRegistrationClosed;
									@endphp
									<a {{ $regActive ? 'href=' . route('register', ['uid' => $event->event_uid, 'event_type' => $event->event_type]) : '' }}
									   class="block w-full text-center {{ $regActive ? 'bg-[#666666] hover:bg-[#4D4D4D]' : 'bg-[#bbbbbb] cursor-not-allowed' }} text-white py-2 rounded text-lg font-bold uppercase flex items-center justify-center">
										{{ $isRegistrationClosed ? 'ANMÄLAN STÄNGD' : 'HÄMTA LOGIN' }}
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
