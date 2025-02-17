<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Inscripción</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .ticket-container {
            max-width: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin: auto;
            text-align: left;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #007BFF;
            margin: 5px 0;
        }
        p {
            font-size: 14px;
            margin: 5px 0;
        }
        .ticket-info {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
        .qr-code {
            text-align: center;
            margin-top: 15px;
        }
        .footer {
            font-size: 12px;
            color: #555;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h2>🎟 Ticket de Inscripción</h2>
        </div>
        
        <div class="ticket-info">
            <p><strong>Usuario:</strong> {{ $usuario }}</p>
            <p><strong>Evento:</strong> {{ $evento }}</p>
            <p><strong>Precio:</strong> {{ $pago }}</p>
            <p><strong>ID del Ticket:</strong> {{ $eventoid }}</p>
        </div>

        <div class="qr-code">
            <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={{ urlencode($eventoid) }}&choe=UTF-8" alt="{{ $eventoid}}">
        </div>
        

        <div class="footer">
            <p>📍 Presenta este ticket el día del evento.</p>
            <p>📅 ¡Te esperamos!</p>
        </div>
    </div>
</body>
</html>
