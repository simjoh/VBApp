<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased">
<header class="bg-blue-500 py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="700" height="800"  src ="/logo-2024.svg"/>
    </div>
</header>
<div class="container mx-auto p-4">
    <div class="bg-white p-6 rounded-md shadow-md">

        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium"></span><p>Thank you for your registration/reservation. We have sent an confirmations email to the address you provided in
                registration form.</p>
        </div>

    </div>
</div>
</body>
</html>
