<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank you {{$registration->person->firstname}}!</title>
</head>
<body>


Event: {{$event->title}} <br>

Ordernummer: 179420 <br>
Betalsätt:<br>
Betalning mottagen: 2023-05-19 08:44<br>
Hemsida: www.västerbottenbrevet.se<br>
Arrangör: Cykelintresset<br>

<h2>Registration details</h2>
<br>
<strong>Name:</strong> {{$registration->person->firstname}} {{$registration->person->surname}}<br>
<strong>Adress:</strong> {{$registration->person->adress->adress}}<br>
<strong>Zip:</strong> {{$registration->person->adress->postal_code}}<br>
<strong>City:</strong>{{$registration->person->adress->city}}<br>
<strong>Country:</strong>{{$registration->person->adress->country_id}}<br>
<br>
<br>
Club: {{$club->name}}

<br>
Optionals<br>
@foreach ($optionals as $optional)
{{$optional->description}}<br>
@endforeach


<a href="{{$startlistlink}}">Link to starting list</a>
</body>
</html>

