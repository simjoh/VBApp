<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('base')
<body class="antialiased bg-stone-100">
<header class="bg-white py-4 py-4">
    <div class="container sm:p-1 mx-auto">
        <img alt="msr logotyp" width="700" height="800"  src ="{{ asset('logo2024.svg') }}"/>
    </div>
</header>
<div class="container mx-auto p-4">
    <div class="bg-orange-50 p-6 rounded-md shadow-md">
            <span class="font-medium"></span><p>{{$text}}</p>
    </div>
</div>
</body>
</html>
