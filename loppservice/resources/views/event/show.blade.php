<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<body>
<header class="bg-white py-4">
	<div class="container sm:p-1 mx-auto">
		<img alt="msr logotyp" width="75%" height="800" src="{{ asset('ebrevet-kalender.svg') }}"/>
	</div>
</header>
<div class="container mx-auto p-0 font-sans">
	<div class="mb-5">
		<p class="mb-3">Cykelintressets brevet-serie består av distanserna: 200, 300, 400 och 600 km och pågår mellan 11 maj och 13
			juli</p>
		<p>Distanserna kan slutföras i valfri ordning och genomföras som individuella lopp. Brevet populaire-lopp på 100 km arrangeras
			tillsammans med 200 km-breveterna</p>
	</div>
	<div class="container mx-auto px-4 py-6 sm:p-4 font-sans">
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-6">
			<div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center h-[800px]">
				<!-- Placeholder for SVG logo -->
				<div class="w-48 h-48 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
					<span class="text-gray-400">Logo placeholder</span>
				</div>
				
				<div class="flex-1 w-full">
					<h2 class="text-xl font-bold mb-4">BRM 200 UPPSALA</h2>
					
					<div class="w-full space-y-2">
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Distans:</span>
							<span>200 KM</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Höjdmeter:</span>
							<span>1310 M</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startdatum:</span>
							<span>11 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Starttid:</span>
							<span>07:00</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Sista anmälan:</span>
							<span>10 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startort:</span>
							<span>Umeå</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Arrangör:</span>
							<span>Randonneur Stockholm</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Övrigt:</span>
							<span>Cykelintresset brevet-serie</span>
						</div>
					</div>
				</div>

				<div class="w-full space-y-2 mt-4">
					<a href="#" class="block w-full text-[#4B5563] hover:text-[#6B7280] hover:underline">
						Länk till bana
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Startlista
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Länk till anmälan och betalning
					</a>
				</div>
			</div>

			<!-- Duplicate cards for example -->
			<div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center h-[800px]">
				<!-- Placeholder for SVG logo -->
				<div class="w-48 h-48 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
					<span class="text-gray-400">Logo placeholder</span>
				</div>
				
				<div class="flex-1 w-full">
					<h2 class="text-xl font-bold mb-4">BRM 200 OTTONTRÄSK</h2>
					
					<div class="w-full space-y-2">
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Distans:</span>
							<span>200 KM</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Höjdmeter:</span>
							<span>1310 M</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startdatum:</span>
							<span>11 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Starttid:</span>
							<span>07:00</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Sista anmälan:</span>
							<span>10 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startort:</span>
							<span>Umeå</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Arrangör:</span>
							<span>Randonneur Stockholm</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Övrigt:</span>
							<span>Cykelintresset brevet-serie</span>
						</div>
					</div>
				</div>

				<div class="w-full space-y-2 mt-4">
					<a href="#" class="block w-full text-[#4B5563] hover:text-[#6B7280] hover:underline">
						Länk till bana
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Startlista
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Länk till anmälan och betalning
					</a>
				</div>
			</div>

			<div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center h-[800px]">
				<!-- Placeholder for SVG logo -->
				<div class="w-48 h-48 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
					<span class="text-gray-400">Logo placeholder</span>
				</div>
				
				<div class="flex-1 w-full">
					<h2 class="text-xl font-bold mb-4">BRM 200 OTTONTRÄSK</h2>
					
					<div class="w-full space-y-2">
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Distans:</span>
							<span>200 KM</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Höjdmeter:</span>
							<span>1310 M</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startdatum:</span>
							<span>11 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Starttid:</span>
							<span>07:00</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Sista anmälan:</span>
							<span>10 maj</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Startort:</span>
							<span>Umeå</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Arrangör:</span>
							<span>Randonneur Stockholm</span>
						</div>
						<div class="flex items-baseline">
							<span class="font-semibold mr-1">Övrigt:</span>
							<span>Cykelintresset brevet-serie</span>
						</div>
					</div>
				</div>

				<div class="w-full space-y-2 mt-4">
					<a href="#" class="block w-full text-[#4B5563] hover:text-[#6B7280] hover:underline">
						Länk till bana
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Startlista
					</a>
					<a href="#" class="block w-full text-center bg-[#6B7280] hover:bg-[#4B5563] text-white py-2 rounded">
						Länk till anmälan och betalning
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
