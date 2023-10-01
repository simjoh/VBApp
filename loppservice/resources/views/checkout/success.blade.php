<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased bg-stone-100">
<header class="bg-white py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="700" height="800"  src ="{{ asset('logo2024.svg') }}"/>
    </div>
</header>
<div class="container mx-auto">
    <div class="bg-orange-50 p-6 rounded-md shadow-md">
            <span class="font-medium"></span><p>Thank you for your registration/reservation. We have sent a confirmation by email to the address you provided in the registration form.</p>
        <p>Please check that you have received an email. If not then check your spam folder and if found there, please change your spam filter settings for the address "info@midnightsunrandonnee.se" so you will not miss future emails.</p>
    </div>
</div>
</body>
</html>
