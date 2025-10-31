<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de Verificación</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .code { font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 3px; text-align: center; padding: 20px; background: #f3f4f6; border-radius: 8px; margin: 20px 0; }
        .warning { color: #dc2626; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Código de Verificación</h2>
        
        <p>Hemos recibido una solicitud para verificar tu dirección de correo electrónico.</p>
        
        <p>Tu código de verificación es:</p>
        
        <div class="code">{{ $token }}</div>
        
        <p class="warning">⚠️ Este código expirará en 5 minutos por seguridad.</p>
        
        <p>Si no solicitaste este código, puedes ignorar este correo.</p>
        
        <hr>
        <p style="font-size: 12px; color: #666;">
            Este es un mensaje automático, por favor no respondas a este correo.
        </p>
    </div>
</body>
</html>