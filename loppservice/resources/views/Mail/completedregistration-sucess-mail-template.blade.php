<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank you {{$registration->person->firstname}}!</title>
</head>
<body>
<div class="md:container md:mx-auto">

    <h2>Thank you for your prege registration to {{$event->title}} </h2>

    <strong>Ref_nr:</strong>{{$registration->ref_nr}} <br>
    Betalsätt:<br>
    Betalning mottagen:<br>
    Hemsida: www.vasterbottenbrevet.se<br>
    Arrangör: Cykelintresset<br>

    <h2>Registration details</h2>
    <br>
    <strong>Startnumber:{{$registration->startnumber}}</strong><br>
    <strong>Name:</strong> {{$registration->person->firstname}} {{$registration->person->surname}}<br>
    <strong>Date of birth:</strong> {{$registration->person->birthdate}}<br>
    <strong>Adress:</strong> {{$registration->person->adress->adress}}<br>
    <strong>Zip:</strong> {{$registration->person->adress->postal_code}}<br>
    <strong>City:</strong>{{$registration->person->adress->city}}<br>
    <strong>Country:</strong>{{$registration->person->adress->country_id}}<br>
    <strong>Tel:</strong>{{$registration->person->contactinformation->tel}}<br>
    <strong>Club:</strong> {{$club}}
    <br>
    <br>
    <strong>Optionals</strong><br>
    @foreach ($optionals as $optional)
    {{$optional->description}}<br>
    @endforeach

    <h2>Credential for app</h2>
    <strong>Username:</strong> {{$registration->startnumber}}<br>
    <strong>Password:</strong> {{$registration->ref_nr}}<br>

    <a href="{{$startlistlink}}">Link to starting list</a>
</div>
</body>
</html>

