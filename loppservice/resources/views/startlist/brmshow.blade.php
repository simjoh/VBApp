@include('base')
<body class="antialiased bg-gray-200">
<header class="bg-gray-200 py-2 sm:py-4">
    <div class="container mx-auto px-2 sm:px-4 max-w-7xl">
        <img alt="eBrevet logotyp" class="mx-auto w-full max-w-3xl sm:max-w-7xl" src="{{ asset('ebrevet-hamta3.svg') }}"/>
    </div>
</header>

<div class="container mx-auto px-2 sm:px-4 py-3 sm:py-6 max-w-7xl font-sans">
    <div class="text-xl sm:text-2xl font-bold mb-2">{{$event->title}}</div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-4 text-sm sm:text-base">
        <div class="bg-white p-2 sm:p-3 rounded shadow-sm">
            <span class="font-semibold">Distans:</span> {{$event->routeDetail->distance ?? '-'}} km
        </div>
        <div class="bg-white p-2 sm:p-3 rounded shadow-sm">
            <span class="font-semibold">Startdatum:</span> {{isset($event->startdate) ? date('Y-m-d', strtotime($event->startdate)) : '-'}}
        </div>
        <div class="bg-white p-2 sm:p-3 rounded shadow-sm">
            <span class="font-semibold">Starttid:</span> {{isset($event->routeDetail->start_time) ? date('H:i', strtotime($event->routeDetail->start_time)) : '-'}}
        </div>
    </div>

    <div class="overflow-x-auto">
        <div class="overflow-hidden shadow-md rounded-lg">
            <table class="min-w-full text-left text-sm sm:text-md">
                <thead>
                    <tr class="bg-gray-700 text-white">
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">#</th>
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">Efternamn</th>
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">Förnamn</th>
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">Klubb</th>
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">Ort</th>
                        <th scope="col" class="py-2 sm:py-3 px-2 sm:px-4">Land</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($startlista) > 0)
                        @foreach ($startlista as $key => $starlist)
                        <tr class="border-b bg-neutral-100 even:bg-white">
                            <td class="py-2 sm:py-3 px-2 sm:px-4">{{$starlist->startnumber}}</td>
                            <td class="py-2 sm:py-3 px-2 sm:px-4">{{$starlist->surname}}</td>
                            <td class="py-2 sm:py-3 px-2 sm:px-4">{{$starlist->firstname}}</td>
                            <td class="py-2 sm:py-3 px-2 sm:px-4">{{$starlist->club_name}}</td>
                            <td class="py-2 sm:py-3 px-2 sm:px-4">{{$starlist->city}}</td>
                            <td class="py-2 sm:py-3 px-2 sm:px-4">
                                <div class="flex items-center">
                                    <img class="h-4 sm:h-5 mr-1 sm:mr-2" src="{{$starlist->flag_url_png}}" title="{{$starlist->country_name_en}}" alt="{{$starlist->country_name_en}}">
                                    <span class="truncate">{{$starlist->country_name_en}}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    @if (count($startlista) === 0)
                        <tr class="border-b bg-neutral-100">
                            <td colspan="6" class="py-2 sm:py-3 px-2 sm:px-4 text-center">Inga anmälda ännu</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
