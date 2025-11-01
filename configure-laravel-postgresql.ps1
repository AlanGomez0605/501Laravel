# Script para ejecutar DESPUÉS de instalar PostgreSQL
# Ejecutar en PowerShell normal (no necesita ser administrador)

Write-Host "=== Configurando Laravel con PostgreSQL recién instalado ===" -ForegroundColor Green

$psqlPath = "C:\Program Files\PostgreSQL\17\bin\psql.exe"  # Cambiará según la versión instalada

# Verificar que PostgreSQL está instalado
if (!(Test-Path $psqlPath)) {
    $psqlPath = "C:\Program Files\PostgreSQL\16\bin\psql.exe"
    if (!(Test-Path $psqlPath)) {
        Write-Host "❌ PostgreSQL no encontrado. Verifica la instalación." -ForegroundColor Red
        Write-Host "📍 Busca psql.exe en: C:\Program Files\PostgreSQL\[version]\bin\" -ForegroundColor Yellow
        exit
    }
}

Write-Host "✅ PostgreSQL encontrado en: $psqlPath" -ForegroundColor Green

# Comandos SQL para crear usuario y base de datos Laravel
$sqlCommands = @"
-- Crear usuario Laravel
CREATE USER laravel_user WITH PASSWORD 'LaRappa501_Password';

-- Crear base de datos
CREATE DATABASE laravel_auth OWNER laravel_user;

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON DATABASE laravel_auth TO laravel_user;

-- Conectar a la nueva base de datos y otorgar privilegios en el esquema
\c laravel_auth;
GRANT ALL PRIVILEGES ON SCHEMA public TO laravel_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO laravel_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO laravel_user;

-- Salir
\q
"@

Write-Host "🔑 Conectando con contraseña: LaRappa501_PostgreSQL" -ForegroundColor Yellow
Write-Host "📝 Creando usuario y base de datos..." -ForegroundColor Yellow

# Crear archivo temporal con comandos SQL
$tempFile = ".\temp_setup.sql"
$sqlCommands | Out-File -FilePath $tempFile -Encoding UTF8

# Ejecutar comandos SQL
& $psqlPath -U postgres -d postgres -f $tempFile

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Base de datos configurada correctamente!" -ForegroundColor Green
    
    # Limpiar archivo temporal
    Remove-Item $tempFile -Force
    
    # Ejecutar migraciones Laravel
    Write-Host "🚀 Ejecutando migraciones Laravel..." -ForegroundColor Yellow
    php artisan migrate --force
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ ¡Laravel configurado con PostgreSQL exitosamente!" -ForegroundColor Green
        Write-Host "🎉 Tu aplicación ya está lista!" -ForegroundColor Cyan
        
        # Mostrar información de conexión
        Write-Host "`n📊 Información de conexión:" -ForegroundColor White
        Write-Host "  Base de datos: laravel_auth" -ForegroundColor Gray
        Write-Host "  Usuario: laravel_user" -ForegroundColor Gray
        Write-Host "  Contraseña: LaRappa501_Password" -ForegroundColor Gray
        Write-Host "  Host: 127.0.0.1" -ForegroundColor Gray
        Write-Host "  Puerto: 5432" -ForegroundColor Gray
        
    } else {
        Write-Host "❌ Error al ejecutar migraciones" -ForegroundColor Red
        Write-Host "🔍 Verifica la conexión a la base de datos" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ Error al configurar la base de datos" -ForegroundColor Red
    Write-Host "🔍 Verifica que la contraseña sea: LaRappa501_PostgreSQL" -ForegroundColor Yellow
    Remove-Item $tempFile -Force
}