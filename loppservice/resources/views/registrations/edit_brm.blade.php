<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')

<header class="bg-gray-200 py-0">
    <div class="container sm:p-1 mx-auto">
        <img alt="brm logotyp" width="75%" height="800" src="{{ asset('ebrevet-hamta3.svg') }}" />
    </div>
</header>
<body class="antialiased bg-gray-200">
    <!-- Header -->

    <!-- Main Content -->
    <div class="container mx-auto p-0 font-sans">
        <div class="bg-blue-50 p-6 rounded-md shadow-md">
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

                        <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
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
            <h2 class="text-2xl font-semibold mb-4">Uppdatera din BRM-registrering</h2>

            <form method="post" class="grid sm:grid-cols-1 gap-4" action="{{ url('registration.update') }}">
                @method('PUT')
                @csrf
                <hr class="h-1 my-4 bg-gray-900 border-0 dark:bg-gray-700">
                <input type="text" value="{{ $registration->registration_uid }}" hidden="hidden" id="registration_uid"
                    name="registration_uid">
                <div class="border-gray-900/10 pb-3">
                    <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                        <div>
                            <label for="first-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Förnamn</label>
                            <input type="text" value="{{ $registration->firstname }}" id="first-name" name="first_name"
                                class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                                autocomplete="given-name" required>
                        </div>
                        <div>
                            <label for="last-name" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Efternamn</label>
                            <input type="text" value="{{ $registration->surname }}" id="last-name" name="last_name"
                                class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                                autocomplete="family-name" required>
                        </div>
                    </div>

                    <div class="mt-2 w-1/2">
                        <label for="email" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">E-post</label>
                        <input id="email" name="email" disabled type="email" autocomplete="email" value="{{$registration->email}}"
                            class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>

                    <div class="mt-2">
                        <label for="street-address" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Gatuadress</label>
                        <input type="text" name="street-address" id="street-address" autocomplete="street-address"
                            value="{{ $registration->adress}}"
                            class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                    </div>

                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                        <div class="mt-2">
                            <label for="postal-code" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Postnummer</label>
                            <input type="text" name="postal-code" id="postal-code" autocomplete="postal-code"
                                value="{{ $registration->postal_code}}"
                                class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                        </div>
                        <div class="mt-2 mb-4">
                            <label for="city" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Ort</label>
                            <input type="text" name="city" id="city" autocomplete="address-level2" value="{{ $registration->city}}"
                                class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                        <div class="mt-2">
                            <label for="tel" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Telefon</label>
                            <input type="text" name="tel" id="tel" autocomplete="tel" value="{{$registration->tel}}"
                                class="md:w-full  sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                        </div>
                        <div class="mt-2 mb-4">
                            <label for="club" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Officiell BRM-klubb</label>
                            <select name="club_uid" id="club" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                @foreach ($clubs as $club)
                                    <option value="{{ $club->club_uid }}" {{ $registration->club_uid == $club->club_uid ? 'selected' : '' }}>
                                        {{ $club->name }} {{ $club->acp_code ? '('.$club->acp_code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">För BRM-evenemang måste du välja en officiell klubb som erkänns av Audax Club Parisien</p>
                        </div>
                    </div>
                    <p class="text-sm font-medium leading-6 text-gray-900">Födelsedatum</p>
                    <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                        <div class="grid md:grid-cols-3 sm:grid-cols-1 gap-3">
                            <div class="mt-2">
                                <label for="year" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">ÅÅÅÅ</label>
                                <select name="year" id="year"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                    <option>År</option>
                                    @foreach ($years as $year)
                                    @if ($year == $birthyear)
                                    <option value="{{ $year }}" selected>{{ $year }}</option>
                                    @else
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-2">
                                <label for="month" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">MM</label>
                                <select name="month" id="month"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
                                    @foreach ($months as $key => $months)
                                    @if ($key == $month)
                                    <option value="{{ $key }}" selected>{{ $months }}</option>
                                    @else
                                    <option value="{{ $key }}">{{ $months }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2">
                                <label for="day" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">DD</label>
                                <select name="day" id="day"
                                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
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
                    </div>

                    <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                        <label for="gender" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Kön</label>
                        <select name="gender" id="gender"
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:border-blue-600" required>
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

                    <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                        <label for="extra-info" class="block text-gray-900 font-medium sm:text-sm sm:leading-6">Övrig information</label>
                        <input type="text" name="extra-info" id="extra-info" value="{{$registration->additional_information}}"
                            class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600">
                    </div>

                    <hr class="h-1 my-12 bg-gray-900 border-0 dark:bg-gray-700">
                    <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                        <button type="submit" value="reserve" name="save"
                                class="w-full bg-blue-600 text-white py-2 px-4 font-bold rounded-md hover:bg-blue-500 focus:outline-none focus:bg-blue-700">
                            SPARA
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
