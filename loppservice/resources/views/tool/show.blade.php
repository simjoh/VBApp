<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased bg-stone-100">
<!-- Header -->
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2024.svg') }}"/>
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

					<span class="absolute top-0 bottom-0 right-0 px-4 py-3">
    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
		 viewBox="0 0 20 20"><title>Close</title><path
			d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
  </span>
				</div>
			</ul>
		</div>
		@endif


		<form method="post" class="grid sm:grid-cols-1 gap-4">
			@csrf
			<div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
				<button type="submit" value="run" name="save"
						class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600">
					Run migrate
				</button>

				<a class="text-white bg-orange-500 hover:bg-blue-800 focus:ring-4 focus:ring-orange-300 font-medium  text-sm px-5 py-2.5 dark:bg-orange-600 dark:hover:bg-orange-600 focus:outline-none dark:focus:ring-orange-800" href="{{ url($callping)}}" class="btn btn-sm btn-primary me-2">
					Test av integration med cykelapp
				</a>

			</div>
	</div>
	</form>
</div>
</div>
</body>

</html>
