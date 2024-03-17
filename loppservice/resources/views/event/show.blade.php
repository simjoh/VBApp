<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<body>
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2024.svg') }}"/>
	</div>
</header>
<div class="container mx-auto p-0 font-sans">
	@foreach ($allevents as $key =>  $event)
	<div class="relative flex py-5 items-center">
		<div class="flex-grow border-t border-gray-400"></div>
		<span class="flex-shrink mx-4 text-gray-400">{{$key}}</span>
		<div class="flex-grow border-t border-gray-400"></div>
	</div>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
		@foreach ($event as  $monthevent)
		<div class="max-w-sm rounded overflow-hidden shadow-lg">
			<img class="w-full" src="{{ asset('logo2024.svg') }}" alt="{{$monthevent->title}}">
			<div class="px-6 py-4">
				<div class="font-bold text-xl mb-2">{{$monthevent->title}}</div>
				<p class="text-gray-700 text-base">
					{{$monthevent->description}}
				</p>
			</div>
			<div class="px-6 pt-4 pb-2">
				<button
					onclick="window.location='{{ route('register', $monthevent->event_uid)  . '?' . http_build_query(['event_type'=>'BRM']) }}'"
					class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 border border-blue-700 rounded">
					Till anmälan
				</button>
			</div>
		</div>
		@endforeach
	</div>
	@endforeach
</div>
</body>
</html>
