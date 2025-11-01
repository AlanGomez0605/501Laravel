# Script para desinstalar y reinstalar PostgreSQL limpio
# Ejecutar como ADMINISTRADOR

Write-Host "=== Reinstalación limpia de PostgreSQL ===" -ForegroundColor Green

# 1. Detener servicios
Write-Host "1. Deteniendo servicios PostgreSQL..." -ForegroundColor Yellow
Get-Service -Name "*postgres*" | Stop-Service -Force -ErrorAction SilentlyContinue

# 2. Desinstalar PostgreSQL
Write-Host "2. Desinstalando PostgreSQL..." -ForegroundColor Yellow
$uninstaller = "C:\Program Files\PostgreSQL\18\uninstall-postgresql.exe"
if (Test-Path $uninstaller) {
    & $uninstaller --mode unattended
}

# 3. Limpiar directorios
Write-Host "3. Limpiando directorios..." -ForegroundColor Yellow
Remove-Item "C:\Program Files\PostgreSQL" -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item "C:\postgresql" -Recurse -Force -ErrorAction SilentlyContinue

# 4. Descargar PostgreSQL
Write-Host "4. Abriendo página de descarga..." -ForegroundColor Yellow
Start-Process "https://www.enterprisedb.com/downloads/postgres-postgresql-downloads"

Write-Host "✅ Desinstalación completada" -ForegroundColor Green
Write-Host "📥 Descarga PostgreSQL desde la página que se abrió" -ForegroundColor Cyan
Write-Host "🔑 Durante la instalación, usa la contraseña: LaRappa501_PostgreSQL" -ForegroundColor Cyan