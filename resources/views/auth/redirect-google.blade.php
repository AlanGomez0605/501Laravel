<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirigiendo a Google...</title>
    <style>
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f9fafb;
        }
        .container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .text {
            color: #374151;
            margin-bottom: 1rem;
        }
        .subtext {
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        <div class="text">
            <h2>Redirigiendo a Google...</h2>
            <p>Este correo ya está registrado con Google.</p>
        </div>
        <div class="subtext">
            Si no eres redirigido automáticamente, <a href="{{ route('google.login') }}" style="color: #3b82f6;">haz clic aquí</a>.
        </div>
    </div>

    <script>
        // Redirigir después de 2 segundos
        setTimeout(function() {
            window.location.href = "{{ route('google.login') }}";
        }, 2000);
    </script>
</body>
</html>