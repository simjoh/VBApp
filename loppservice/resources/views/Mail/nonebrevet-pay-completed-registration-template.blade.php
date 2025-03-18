<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bekräftelse på anmälan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
        .login-info {
            background-color: #e9f7ff;
            border: 1px solid #c5e4ff;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .login-info h3 {
            margin-top: 0;
            color: #0066cc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bekräftelse på anmälan</h1>
        </div>
        
        <div class="content">
            <p>Hej {{ $person->firstname ?? '' }} {{ $person->lastname ?? '' }},</p>
            
            <p>Tack för din anmälan till {{ $event->title ?? '' }}.</p>
            
            <!-- Login information section -->
            <div class="login-info">
                <h3>Inloggningsinformation</h3>
                <p>Du kan logga in med följande uppgifter:</p>
                <p><strong>Användarnamn:</strong> {{ $registration->startnumber ?? 'Ej tilldelat' }}</p>
                <p><strong>Lösenord:</strong> {{ $registration->ref_nr ?? '' }}</p>

                <p><a href="https://ebrevet.org/app">Logga in på eBrevet</a></p>
                <p><a href="https://www.ebrevet.org/sv/">Användar manual</a></p>
                <p><a href="https://www.ebrevet.org/loppservice/public/events">Brevet kalender</a></p>
            </div>
            
            <!-- Main content will go here -->
            
            <p>Arrangör: {{ $organizer ?? '' }}</p>
            
            @if(isset($startlistlink) && !empty($startlistlink))
            <p>
                <a href="{{ $startlistlink }}">Se startlista</a>
            </p>
            @endif
            
            @if(isset($updatelink) && !empty($updatelink))
            <p>
                <a href="{{ $updatelink }}">Uppdatera din anmälan</a>
            </p>
            @endif
        </div>
        
        <div class="footer">
            <p>Detta är ett automatiskt genererat meddelande. Vänligen svara inte på detta mail.</p>
        </div>
    </div>
</body>
</html> 