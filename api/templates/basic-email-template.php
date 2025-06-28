<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $subject ?? 'Email from Västerbottenbrevet' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $subject ?? 'Meddelande från Västerbottenbrevet' ?></h1>
    </div>
    
    <div class="content">
        <?= $content ?? '' ?>
    </div>
    
    <div class="footer">
        <p>Med vänliga hälsningar,<br>Västerbottenbrevet</p>
    </div>
</body>
</html> 