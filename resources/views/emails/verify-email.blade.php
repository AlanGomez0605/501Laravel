<x-mail::message>
# Verificación de Correo Electrónico

Gracias por registrarte. Tu código de verificación es:

<div style="text-align: center; font-size: 24px; font-weight: bold; padding: 20px; background-color: #f8f9fa; margin: 20px 0;">
    {{ $token }}
</div>

Este código expirará en 5 minutos.

Si no has solicitado este código, puedes ignorar este correo.

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>