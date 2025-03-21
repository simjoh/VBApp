@include('base')
<header class="bg-[#aaaaaa]">
    <div class="container mx-auto px-2 sm:px-4 max-w-7xl">
        <img alt="eBrevet logotyp" class="mx-auto w-full max-w-3xl sm:max-w-7xl" src="{{ asset('ebrevet-rando-startlista.svg') }}"/>
    </div>
</header>
<body class="antialiased bg-[#aaaaaa]">
<div class="container mx-auto px-2 sm:px-4  max-w-7xl font-sans mt-0.5">

<div class="bg-[#dddddd] mb-6 p-6 shadow-sm">
<div class="text-xl sm:text-2xl font-bold mb-2">{{$event->title}}</div>
<p>Startdatum: {{isset($event->startdate) ? date('Y-m-d', strtotime($event->startdate)) : '-'}}</p>
<p>Starttid: {{isset($event->routeDetail->start_time) ? date('H:i', strtotime($event->routeDetail->start_time)) : '-'}}</p>
</div>


    <div class="overflow-x-auto">
        <div class="overflow-hidden shadow-md rounded-lg">
            <table class="min-w-full text-left text-sm sm:text-md">
                <thead>
                    <tr class="bg-[#f5e4a3]  text-black">
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
