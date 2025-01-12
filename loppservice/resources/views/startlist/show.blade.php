@include('base')

<body class="bg-stone-100">
    <header class="bg-white py-4">
        <div class="container sm:p-1 mx-auto">
            <img class="px-2" alt="MSR Logotyp" width="75%" height="800" src="{{ asset('logo2025.svg') }}" />
        </div>
    </header>

    <main class="container mx-auto p-0 font-sans">
        <div class="bg-orange-50 p-6 shadow-md rounded-lg">


            <!-- Table for Medium and Larger Screens -->
            <div class="hidden md:block overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-1 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-left text-md font-light">
                            <thead class="border-b bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600">
                                <tr class="bg-orange-50 border-b-1 border-black">
                                    <th scope="col" class="py-3 px-2">#</th>
                                    <th scope="col" class="py-3 px-2">Last name</th>
                                    <th scope="col" class="py-3 px-2">First name</th>
                                    <th scope="col" class="py-3 px-2">Club</th>
                                    <th scope="col" class="py-3 px-2">City</th>
                                    <th scope="col" class="py-3 px-2">Country</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($startlista as $key => $starlist)
                                    <tr class="border-b bg-neutral-100 dark:border-neutral-300 dark:bg-neutral-200 even:bg-white dark:even:bg-neutral-100 text-black">
                                        <td class="whitespace-nowrap py-3 px-2">{{ $starlist->startnumber }}</td>
                                        <td class="whitespace-nowrap py-3 px-2">{{ $starlist->surname }}</td>
                                        <td class="whitespace-nowrap py-3 px-2">{{ $starlist->firstname }}</td>
                                        <td class="whitespace-nowrap py-3 px-2">{{ $starlist->club_name }}</td>
                                        <td class="whitespace-nowrap py-3 px-2">{{ $starlist->city }}</td>
                                        <td class="whitespace-nowrap py-3 px-2 flex items-center">
                                            <img class="float-left pr-1 w-12 md:w-6 pt-1 pr-1" src="{{ $starlist->flag_url_png }}"
                                                 title="{{ $starlist->country_name_en }}" alt="{{ $starlist->country_name_en }}" />
                                            {{ $starlist->country_name_en }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card Layout for Small Screens -->
            <div class="block md:hidden">
                @foreach ($startlista as $key => $starlist)
                    <div class="border-b bg-white dark:bg-neutral-700 p-4 shadow-md rounded-lg mb-4">
                        <p class="text-sm"><span class="font-bold">#:</span> {{ $starlist->startnumber }}</p>
                        <p class="text-sm"><span class="font-bold">Last name:</span> {{ $starlist->surname }}</p>
                        <p class="text-sm"><span class="font-bold">First name:</span> {{ $starlist->firstname }}</p>
                        <p class="text-sm"><span class="font-bold">Club:</span> {{ $starlist->club_name }}</p>
                        <p class="text-sm"><span class="font-bold">City:</span> {{ $starlist->city }}</p>
                        <p class="text-sm flex items-center">
                            <span class="font-bold">Country:</span>
                            <img class="float-left pl-1 pr-1 h-4 w-7 md:w-6 pt-1 pr-1" src="{{ $starlist->flag_url_png }}"
                                 title="{{ $starlist->country_name_en }}" alt="{{ $starlist->country_name_en }}" />
                            {{ $starlist->country_name_en }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</body>
