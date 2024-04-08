<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased">
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="brm logotyp" width="700" height="800" src="{{ asset('cykelintresset.svg') }}"/>
	</div>
</header>
<div class="container mx-auto">
	<div class="flex-grow border-t border-4  border-black"></div>
	<div class="mt-3 bg-gray-100 p-6 rounded-md shadow-md">
		<span class="font-medium"></span>
		<p><strong>{{$message}}</strong></p>
		<p>{{$checkemailmessage}}</p>
	</div>
</div>
</body>
</html>
