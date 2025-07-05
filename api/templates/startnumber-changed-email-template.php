<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Startnummer ändrat</title>
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
        .change-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .change-notice h3 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Startnummer ändrat</h1>
        </div>
        
        <div class="content">
            <p>Hej <?php echo htmlspecialchars($competitor->getGivenName() ?? ''); ?> <?php echo htmlspecialchars($competitor->getFamilyName() ?? ''); ?>,</p>
            
            <p>Ditt startnummer för <?php echo htmlspecialchars($track->getTitle() ?? ''); ?> har ändrats.</p>
            
            <!-- Change notice section -->
            <div class="change-notice">
                <h3>Viktig information</h3>
                <p>Ditt startnummer har ändrats från <strong><?php echo htmlspecialchars($oldStartNumber ?? 'N/A'); ?></strong> till <strong><?php echo htmlspecialchars($participant->getStartnumber() ?? 'N/A'); ?></strong>.</p>
                <p>Detta påverkar dina inloggningsuppgifter för eBrevet.</p>
            </div>
            
            <!-- Login information section -->
            <div class="login-info">
                <h3>Uppdaterade inloggningsinformation</h3>
                <p>Du kan nu logga in med följande uppgifter:</p>
                <p><strong>Användarnamn:</strong> <?php echo htmlspecialchars($participant->getStartnumber() ?? 'Ej tilldelat'); ?></p>
                <p><strong>Referensnummer:</strong> <?php echo htmlspecialchars($refNr ?? 'Ej tillgängligt'); ?></p>

                <p><a href="https://ebrevet.org/app">Logga in på eBrevet</a></p>
                <p><a href="https://www.ebrevet.org/sv/">Användar manual</a></p>
                <p><a href="https://www.ebrevet.org/loppservice/public/events">Brevet kalender</a></p>
            </div>
            
            <p><strong>Event:</strong> <?php echo htmlspecialchars($track->getTitle() ?? ''); ?></p>
            <p><strong>Datum:</strong> <?php echo htmlspecialchars($track->getStartDateTime() ?? ''); ?></p>
            <p><strong>Arrangör:</strong> <?php echo htmlspecialchars($organizer ?? ''); ?></p>
            
            <p>Om du har några frågor, vänligen kontakta arrangören.</p>
        </div>
        
        <div class="footer">
            <p>Detta är ett automatiskt genererat meddelande. Vänligen svara inte på detta mail.</p>
        </div>
    </div>
</body>
</html> 