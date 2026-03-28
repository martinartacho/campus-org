<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="date=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">
    <title>{{ $notification->title }}</title>
    <style>
        /* Reset styles */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        
        /* Main styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 15px 0;
        }
        .footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $notification->title }}</h1>
        </div>
        
        <div class="content">
            <h3>Hola {{ $user->name }},</h3>
            
            <div>{!! $processedContent !!}</div>
            
            @if(config('app.url'))
            <p>
                <a href="{{ config('app.url') }}/notifications" class="btn">
                    Veure totes les notificacions
                </a>
            </p>
            @endif
        </div>
        
        <div class="footer">
            <p>
                <strong>Enviat per:</strong> {{ $notification->sender->name ?? 'Sistema' }}<br>
                <strong>Data:</strong> {{ $notification->created_at->format('d/m/Y H:i') }}<br>
                <small>Aquest és un missatge automàtic, si us plau no responguis a aquest email.</small>
            </p>
        </div>
    </div>
</body>
</html>