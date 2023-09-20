@include('base')
<div class="md:container md:mx-auto">
    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-1 sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="min-w-full text-left text-sm font-light">
                        <thead
                            class="border-b bg-white font-medium dark:border-neutral-500 dark:bg-neutral-600">
                        <tr>
                            <th scope="col" class="py-1">#</th>
                            <th scope="col" class="py-1">Firstname</th>
                            <th scope="col" class="py-1">Lastname</th>
                            <th scope="col" class="py-1">Club</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($startlista as $key => $starlist)
                        <tr class="border-b bg-neutral-100 dark:border-neutral-500 dark:bg-neutral-700">
                            <td class="whitespace-nowrap py-2 font-medium">{{$starlist->startnumber}}</td>
                            @foreach ($countries as $country)
                            @if($country->country_id == $starlist->person->adress->country_id)
                            <td class="whitespace-nowrap py-2 "><img class="float-left mr-1 pt-1"
                                                                                      src="{{$country->flag_url_png}}"
                                                                                      alt="Contryname"
                                                                                      width="20" height="20">{{$starlist->person->firstname}}
                            </td>
                            @endif
                            @endforeach

                            <td class="whitespace-nowrap py-1">{{$starlist->person->surname}}{{$starlist->test}}</td>
                            @foreach ($clubs as $club)
                            @if($club->club_uid == $starlist->club_uid)
                            <td class="whitespace-nowrap py-1">{{$club->name}}</td>
                            @endif
                            @endforeach
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

