@include('base')
<body class="antialiased">
    <div class="md:container md:mx-auto">
        <div class="bg-white p-6 shadow-md">
        <!-- Header -->
        <header class="bg-white py-4">
            <div class="container sm:p-1 mx-auto">
                <img alt="msr logotyp" width="75%" height="800" src="{{ asset('logo-2024.svg') }}"/>
            </div>
        </header>
        <!-- Main Content -->

        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-1 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-left text-sm font-light">
                            <thead class="border-b bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600">
                            <tr>
                                <th scope="col" class="py-1">#</th>
                                <th scope="col" class="py-1"></th>
                                <th scope="col" class="py-1">Name</th>
                                <th scope="col" class="py-1">Club</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($startlista as $key => $starlist)
                            <tr class="border-b bg-neutral-100 dark:border-neutral-500 dark:bg-neutral-700 even:bg-white">
                                <td class="whitespace-nowrap py-1">{{$starlist->startnumber}}</td>
                                <td>
                                    <img class="float-left pr-1 w-12 md:w-6" src="{{$starlist->flag_url_png}}" title="{{$starlist->country_name_en}}" alt="{{$starlist->country_name_en}}">
                                </td>
                                <td>
                                    {{$starlist->firstname}}
                                    {{$starlist->surname}}
                                </td>
                                <td class="whitespace-nowrap py-1">{{$starlist->club_name}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</body>
