@include('base')
<body>
<header class="bg-white py-4">
<!--	<div class="container sm:p-1 mx-auto">-->
<!--		<img class="px-2" alt="msr logotyp" width="75%" height="800" src="{{ asset('cykelintresset.svg') }}"/>-->
<!--	</div>-->
</header>
<div class="container mx-auto p-0 font-sans">
	<div class="text-2xl font-bold">{{$event->title}} &nbsp;</div>
	<div class="flex flex-col">
		<div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
			<div class="inline-block min-w-full py-1 sm:px-6 lg:px-8">
				<div class="overflow-hidden">
					<table class="min-w-full text-left text-md font-light">
						<thead class="border-b text-white bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600">
						<tr class="bg-gray-700 border-b-1 border-black">
							<th scope="col" class="py-3 px-2">#</th>
							<th scope="col" class="py-3 px-2">Efternamn</th>
							<th scope="col" class="py-3 px-2">Förnamn</th>
							<th scope="col" class="py-3 px-2">Klubb</th>
							<th scope="col" class="py-3 px-2">Ort</th>
							<th scope="col" class="py-3 px-2">Land</th>
						</tr>
						</thead>
						<tbody>

						@if (count($startlista) > 0)
						@foreach ($startlista as $key => $starlist)
						<tr class="border-b bg-neutral-100 dark:border-neutral-500 dark:bg-neutral-700 even:bg-white">
							<td class="whitespace-nowrap py-3 px-2">{{$starlist->startnumber}}</td>
							<td class="whitespace-nowrap py-3 px-2">{{$starlist->surname}}</td>
							<td class="whitespace-nowrap py-3 px-2">{{$starlist->firstname}}</td>
							<td class="whitespace-nowrap py-3 px-2">{{$starlist->club_name}}</td>
							<td class="whitespace-nowrap py-3 px-2">{{$starlist->city}}</td>
							<td class="whitespace-nowrap py-3 px-2"><img class="float-left pr-1 w-12 md:w-6 pt-1 pr-1" src="{{$starlist->flag_url_png}}" title="{{$starlist->country_name_en}}" alt="{{$starlist->country_name_en}}">{{$starlist->country_name_en}}</td>
						</tr>
						@endforeach
						@endif
						@if (count($startlista) === 0)
						<tr class="border-b bg-neutral-100 dark:border-neutral-500 dark:bg-neutral-700 even:bg-white">
							<td colSpan={6}>Inga anmälda ännu</td>

						</tr>
						@endif

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>

</body>
