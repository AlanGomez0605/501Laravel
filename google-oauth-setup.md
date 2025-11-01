# Instrucciones para actualizar Google OAuth Console

## 1. Ve a Google Cloud Console:
https://console.cloud.google.com/

## 2. Selecciona tu proyecto existente

## 3. Ve a "APIs & Services" > "Credentials"

## 4. Edita tu OAuth 2.0 Client ID existente

## 5. En "Authorized redirect URIs", AÑADE estas URLs:
- https://tudominio.com/auth/google/callback
- https://www.tudominio.com/auth/google/callback

## 6. En "Authorized JavaScript origins", AÑADE:
- https://tudominio.com
- https://www.tudominio.com

## MANTÉN TAMBIÉN las URLs locales para desarrollo:
- http://127.0.0.1:8000/auth/google/callback
- https://vainglorious-nonsophistical-rosalva.ngrok-free.dev/auth/google/callback

## 7. Guarda los cambios