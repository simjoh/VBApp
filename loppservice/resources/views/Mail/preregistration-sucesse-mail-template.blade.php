<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank you {{$registration->person->firstname}}!</title>
</head>
<body>
<div class="md:container md:mx-auto">
    <h2>Thank you for your prege registration to {{$event->title}} </h2>

    <p> Please remember to make your final registration payment of ?? EUR by bank transfer no later than the {{$registration->reservation_valid_until}}. See payment information :<p>
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


    <p>Payment information</p>
    <a href="{{$completeregistrationlink}}">Follow the link to complete your registration</a><br>

    <a href="{{$startlistlink}}">Link to starting list</a>



    <p>Thank you</p>
</div>
</body>
</html>



