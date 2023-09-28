<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank you {{$registration->person->firstname}}!</title>
</head>
<body>
<div class="md:container md:mx-auto">

    <h2>Thank you for your registration to {{$event->title}}</h2>
    <strong>Ref_nr:</strong>{{$registration->ref_nr}} <br>
    <strong>payment method:</strong> <br>
    <strong>Payment received:</strong><br>
    <strong>Homepage:</strong><a href="https://www.midnightsunrandonnee.se">www.midnightsunrandonnee.se</a><br>
    <strong>Organizer</strong> Cykelintresset<br>

    <h2>Registration details</h2>
    <strong>Startnumber:</strong>{{$registration->startnumber}}<br>
    <strong>Name:</strong> {{$registration->person->firstname}} {{$registration->person->surname}}<br>
    <strong>Date of birth:</strong> {{$registration->person->birthdate}}<br>
    <strong>Adress:</strong> {{$registration->person->adress->adress}}<br>
    <strong>Zip:</strong> {{$registration->person->adress->postal_code}}<br>
    <strong>City:</strong>{{$registration->person->adress->city}}<br>
    <strong>Country:</strong>{{$country}}<br>
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

    <strong>Current startlist</strong><br>
    <a href="{{$startlistlink}}">Link to starting list</a><br>


    <strong>Other useful resources</strong>
</div>
</body>
</html>

