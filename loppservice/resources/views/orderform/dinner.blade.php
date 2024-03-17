@include('base')
<body class=" bg-stone-100">
<header class="bg-white py-4">
    <div class="container sm:p-1 mx-auto">
        <img class="px-2" alt="msr logotyp" width="75%" height="800" src="{{ asset('logo2024.svg') }}"/>
    </div>
</header>
<div class="container mx-auto p-0 font-sans">
    <div class="bg-orange-50 p-4 shadow-md">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>

                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Something went wrong</strong>
                    @foreach ($errors->all() as $error)
                    <span class="block sm:inline"><li>{{ $error }}</li></span>
                    @endforeach

                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 20 20"><title>Close</title><path
            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
  </span>
                </div>
            </ul>
        </div>
        @endif

        <form method="post" class="grid sm:grid-cols-1 gap-4">
            @csrf

            <hr class="h-1 my-4 bg-gray-900 border-0 dark:bg-gray-700">
            <div class="border-gray-900/10 pb-3">
                <div class="grid md:grid-cols-2 gap-3 mt-3 sm:grid-cols-1">
                    <div>
                        <label for="first-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">First name</label>
                        <input type="text" id="first-name" name="first_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="given-name" required>
                    </div>
                    <div>
                        <label for="last-name" class="block text-gray-900 font-semibold sm:text-base sm:leading-6">Last name</label>
                        <input type="text" id="last-name" name="last_name"
                               class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"
                               autocomplete="family-name" required>
                    </div>
                </div>

            </div>

            <div class="grid md:grid-cols-2 sm:grid-cols-1 gap-3">
                <div class="mt-2">

                    <label for="email" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email address*</label>
                    <input id="email" name="email" type="email" autocomplete="email"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>
                <div class="mt-2 mb-4">
                    <label for="email-confirm" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Email
                        confirmation*</label>
                    <input id="email-confirm" name="email-confirm" type="email"
                           class="w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
                </div>
            </div>

            <div class="mt-2 w-1/2">
                <label for="tel" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Mobile number</label>
                <input type="text" name="tel" id="tel" autocomplete="tel-level2" autocomplete="tel"
                       class=" w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600" required>
            </div>

            <div class="mt-5 mb-5 md:w-1/2 sm:w-full">
                <label for="extra-info" class="block text-gray-900 font-semibold sm:text-base sm:leading-10">Special dietary
                    requirements</label>
                <p class="mt-1 mb-2 text-base leading-6 text-gray-600">Please let us know if you will have any special requirements
                    concerning the event’s food menu, for instance allergies, gluten or lactose intolerance, vegan or vegetarian
                    meals etc.</p>
                <textarea id="extra-info" name="extra-info" rows="1"
                          class="sm:w-full px-3 py-2 border-2 focus:outline-none focus:border-gray-600"></textarea>
            </div>
            <hr class="h-1 my-4 bg-gray-900 border-0 dark:bg-gray-700">
            <div class="grid md:grid-cols-2 gap-3 mt-4 sm:grid-cols-1">
                <button type="submit" value="{{$dinnerproduct}}" name="save"
                        class="w-full bg-orange-500 text-white py-2 px-4 font-bold rounded-md hover:bg-orange-400 focus:outline-none focus:bg-orange-600">
                    Buy Dinner Ticket
                </button>

            </div>

        </form>
    </div>
</div>
</body>
