# Script para resetear contraseña de PostgreSQL
# Ejecutar como ADMINISTRADOR

Write-Host "=== Reseteando contraseña de PostgreSQL ===" -ForegroundColor Green

$pgPath = "C:\Program Files\PostgreSQL\18"
$dataPath = "$pgPath\data"
$configFile = "$dataPath\pg_hba.conf"

try {
    # 1. Detener el servicio PostgreSQL
    Write-Host "1. Deteniendo PostgreSQL..." -ForegroundColor Yellow
    Stop-Service postgresql-x64-18 -Force

    # 2. Hacer backup del archivo de configuración
    Write-Host "2. Haciendo backup de configuración..." -ForegroundColor Yellow
    Copy-Item $configFile "$configFile.backup"

    # 3. Modificar pg_hba.conf para permitir acceso sin contraseña
    Write-Host "3. Modificando configuración temporal..." -ForegroundColor Yellow
    $content = Get-Content $configFile
    $newContent = $content -replace "md5", "trust"
    $newContent | Set-Content $configFile

    # 4. Reiniciar PostgreSQL
    Write-Host "4. Iniciando PostgreSQL..." -ForegroundColor Yellow
    Start-Service postgresql-x64-18

    Start-Sleep 3

    # 5. Conectar y cambiar contraseña
    Write-Host "5. Cambiando contraseña..." -ForegroundColor Yellow
    $sqlCommand = "ALTER USER postgres PASSWORD 'LaRappa501_PostgreSQL';"
    echo $sqlCommand | & "$pgPath\bin\psql.exe" -U postgres -d postgres

    # 6. Crear usuario Laravel
    Write-Host "6. Creando usuario Laravel..." -ForegroundColor Yellow
    $larravelCommands = @"
CREATE USER laravel_user WITH PASSWORD 'LaRappa501_Password';
CREATE DATABASE laravel_auth OWNER laravel_user;
GRANT ALL PRIVILEGES ON DATABASE laravel_auth TO laravel_user;
GRANT ALL PRIVILEGES ON SCHEMA public TO laravel_user;
"@
    
    echo $larravelCommands | & "$pgPath\bin\psql.exe" -U postgres -d postgres

    # 7. Restaurar configuración original
    Write-Host "7. Restaurando configuración..." -ForegroundColor Yellow
    Stop-Service postgresql-x64-18 -Force
    Copy-Item "$configFile.backup" $configFile
    Start-Service postgresql-x64-18

    Write-Host "✅ Contraseña reseteada exitosamente!" -ForegroundColor Green
    Write-Host "🔑 Nueva contraseña de postgres: LaRappa501_PostgreSQL" -ForegroundColor Cyan
    Write-Host "🔑 Usuario Laravel: laravel_user / LaRappa501_Password" -ForegroundColor Cyan

} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Restaurando configuración..." -ForegroundColor Yellow
    
    if (Test-Path "$configFile.backup") {
        Copy-Item "$configFile.backup" $configFile
        Start-Service postgresql-x64-18
    }
}