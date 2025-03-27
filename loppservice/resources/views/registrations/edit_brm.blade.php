<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<header class="bg-[#aaaaaa] py-0">
    <div class="container mx-auto px-2 sm:px-4 max-w-7xl">
        <img alt="msr logotyp" class="mx-auto w-[200px] w-full" src="{{ asset('ebrevet-rando-anmalan.svg') }}" />
    </div>
</header>

<body class="antialiased bg-[#aaaaaa]">
    <!-- Main Content -->
    <div class="container mx-auto px-2 sm:px-2 lg:px-4 font-sans max-w-7xl">


        <!-- <div class="bg-blue-50 p-6 rounded-md shadow-md mt-4"> -->
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Något gick fel</strong>
                    @foreach ($errors->all() as $error)
                    <span class="block sm:inline">
                        <li>{{ $error }}</li>
                    </span>
                    @endforeach

                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <title>Stäng</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            </ul>
        </div>
        @endif




        <div class="bg-[#dddddd] mb-6 p-6 mt-0.5">
            <!-- Event title and date -->
            <p class="font-semibold mb-4">Uppdatera din BRM-registrering</p>

        </div>


        <form method="post" class="space-y-6" action="{{ url('registration.update') }}">
            @method('PUT')
            @csrf
            <div class="border-t-4 border-[#dddddd] my-6"></div>
            <input type="text" value="{{ $registration->registration_uid }}" hidden="hidden" id="registration_uid"
                name="registration_uid">
            <div class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-semibold text-sm sm:text-base">Förnamn</label>
                        <input type="text" value="{{ $registration->firstname }}" id="first-name" name="first_name"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
                            autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-semibold text-sm sm:text-base">Efternamn</label>
                        <input type="text" value="{{ $registration->surname }}" id="last-name" name="last_name"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600"
                            autocomplete="family-name" required>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-gray-900 font-semibold text-sm sm:text-base">E-post</label>
                    <input id="email" name="email" disabled type="email" autocomplete="email" value="{{$registration->email}}"
                        class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                </div>

                <div>
                    <label for="street-address" class="block text-gray-900 font-semibold text-sm sm:text-base">Gatuadress</label>
                    <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                        value="{{ $registration->adress}}"
                        class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="postal-code" class="block text-gray-900 font-semibold text-sm sm:text-base">Postnummer</label>
                        <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                            value="{{ $registration->postal_code}}"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div>
                        <label for="city" class="block text-gray-900 font-semibold text-sm sm:text-base">Ort</label>
                        <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ $registration->city}}"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tel" class="block text-gray-900 font-semibold text-sm sm:text-base">Telefon</label>
                        <input type="text" name="tel" id="tel" autocomplete="tel" value="{{$registration->tel}}"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                    </div>
                    <div>
                        <label for="club" class="block text-gray-900 font-semibold text-sm sm:text-base">Officiell BRM-klubb</label>
                        <select name="club_uid" id="club"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                            @foreach ($clubs as $club)
                            <option value="{{ $club->club_uid }}" {{ $registration->club_uid == $club->club_uid ? 'selected' : '' }}>
                                {{ $club->name }} {{ $club->acp_code ? '('.$club->acp_code.')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">För BRM-evenemang måste du välja en officiell klubb som erkänns av Audax Club Parisien</p>
                    </div>
                </div>

                <div>
                    <p class="block text-gray-900 font-semibold text-sm sm:text-base">Födelsedatum</p>
                    <div class="grid grid-cols-3 gap-4 sm:w-1/2">
                        <select name="year" id="year"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                            <option>År</option>
                            @foreach ($years as $year)
                            @if ($year == $birthyear)
                            <option value="{{ $year }}" selected>{{ $year }}</option>
                            @else
                            <option value="{{ $year }}">{{ $year }}</option>
                            @endif
                            @endforeach
                        </select>

                        <select name="month" id="month"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                            @foreach ($months as $key => $months)
                            @if ($key == $month)
                            <option value="{{ $key }}" selected>{{ $months }}</option>
                            @else
                            <option value="{{ $key }}">{{ $months }}</option>
                            @endif
                            @endforeach
                        </select>

                        <select name="day" id="day"
                            class="mt-1 block w-full px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                            <option value="">Dag</option>
                            @foreach ($days as $key => $daysa)
                            @if ($daysa == $day)
                            <option value="{{ $key }}" selected>{{ $daysa }}</option>
                            @else
                            <option value="{{ $key }}">{{ $daysa }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="gender" class="block text-gray-900 font-semibold text-sm sm:text-base">Kön</label>
                    <select name="gender" id="gender"
                        class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600" required>
                        <option>Kön</option>
                        @foreach ($genders as $key => $gender)
                        @if ($key == $registration->gender)
                        <option value="{{ $key }}" selected>{{ $gender }}</option>
                        @else
                        <option value="{{ $key }}">{{ $gender }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="extra-info" class="block text-gray-900 font-semibold text-sm sm:text-base">Övrig information</label>
                    <textarea name="extra-info" id="extra-info" rows="3" maxlength="100"
                        class="mt-1 block w-full sm:w-1/2 px-3 py-2 border-2 rounded-md focus:outline-none focus:border-gray-600">{{$registration->additional_information}}</textarea>
                </div>

                <div class="border-t-4 border-[#dddddd] my-6"></div>

                <div>
                    <button type="submit" value="reserve" name="save"
                        class="w-full sm:w-auto px-6 py-3 bg-[#f5e4a3] text-[#3780b5] font-bold rounded-md hover:bg-[#f0dfa0] focus:outline-none focus:bg-[#e8d795]">
                        SPARA
                    </button>
                </div>
            </div>
        </form>
        <!-- </div> -->
    </div>
</body>

</html>
