# Script para configurar PostgreSQL en Laravel
# Ejecutar como administrador

Write-Host "=== Configurando PostgreSQL para Laravel ===" -ForegroundColor Green

# 1. Conectar a PostgreSQL como superusuario
Write-Host "1. Conectando a PostgreSQL..." -ForegroundColor Yellow
$psqlPath = "C:\Program Files\PostgreSQL\18\bin\psql.exe"

# 2. Crear usuario y base de datos
$sqlCommands = @"
-- Crear usuario Laravel
CREATE USER laravel_user WITH PASSWORD 'LaRappa501_Password';

-- Crear base de datos
CREATE DATABASE laravel_auth OWNER laravel_user;

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON DATABASE laravel_auth TO laravel_user;
GRANT ALL PRIVILEGES ON SCHEMA public TO laravel_user;

-- Salir
\q
"@

Write-Host "2. Creando usuario y base de datos..." -ForegroundColor Yellow
$sqlCommands | & $psqlPath -U postgres -d postgres

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Base de datos configurada correctamente" -ForegroundColor Green
    
    # 3. Ejecutar migraciones
    Write-Host "3. Ejecutando migraciones de Laravel..." -ForegroundColor Yellow
    php artisan migrate --force
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Migraciones ejecutadas correctamente" -ForegroundColor Green
        Write-Host "🚀 Laravel está listo para usar PostgreSQL!" -ForegroundColor Cyan
    } else {
        Write-Host "❌ Error al ejecutar migraciones" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Error al configurar PostgreSQL" -ForegroundColor Red
    Write-Host "Intenta ejecutar manualmente:" -ForegroundColor Yellow
    Write-Host "psql -U postgres -d postgres" -ForegroundColor White
}