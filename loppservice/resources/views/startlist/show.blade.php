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
                        {{$startlista}}
                        @foreach ($startlista as $key => $starlist)
                        <tr class="border-b bg-neutral-100 dark:border-neutral-500 dark:bg-neutral-700">
                            <td class="whitespace-nowrap px-6 py-1 font-medium">{{$starlist->startnumber}}</td>
                            <td>
                                <img class="whitespace-nowrap px-6 py-1 inline-block">
                                <img class="float-left" src="{{$starlist->flag_url}}" alt="Contryname" width="20" height="20">
                                {{$starlist->firstname}}
                            </td>
                            <td class="whitespace-nowrap px-6 py-1">{{$starlist->surname}}</td>
                            <td class="whitespace-nowrap px-6 py-1">{{$starlist->club_name}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

