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
<div class="bg-[#dddddd] mb-6 p-6 mt-0.5">
        <!-- Event title and date -->
		<p><strong>{{$message}}</strong></p>
		<p>{{$checkemailmessage}}</p>
	</div>
</div>
</body>
</html>
