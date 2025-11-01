# Script para configurar Laravel con PostgreSQL usando tu contraseña existente
# Ejecutar en PowerShell normal

Write-Host "=== Configurando Laravel con PostgreSQL ===" -ForegroundColor Green

$psqlPath = "C:\Program Files\PostgreSQL\18\bin\psql.exe"

# Verificar que PostgreSQL está disponible
if (!(Test-Path $psqlPath)) {
    Write-Host "❌ PostgreSQL no encontrado en la ruta esperada" -ForegroundColor Red
    exit
}

Write-Host "✅ PostgreSQL encontrado" -ForegroundColor Green

# Crear archivo temporal con comandos SQL
$sqlCommands = @"
-- Crear usuario Laravel (si no existe)
DO `$`$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'laravel_user') THEN
      CREATE USER laravel_user WITH PASSWORD 'LaRappa501_Password';
   END IF;
END
`$`$;

-- Crear base de datos (si no existe)
SELECT 'CREATE DATABASE laravel_auth OWNER laravel_user'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'laravel_auth')\gexec

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON DATABASE laravel_auth TO laravel_user;

-- Conectar a la base de datos Laravel y otorgar privilegios en el esquema
\c laravel_auth;
GRANT ALL PRIVILEGES ON SCHEMA public TO laravel_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO laravel_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO laravel_user;
GRANT ALL ON ALL FUNCTIONS IN SCHEMA public TO laravel_user;

-- Configurar privilegios por defecto para futuras tablas
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO laravel_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO laravel_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO laravel_user;

\q
"@

# Guardar comandos en archivo temporal
$tempFile = "setup_laravel_db.sql"
$sqlCommands | Out-File -FilePath $tempFile -Encoding UTF8

Write-Host "📝 Comandos SQL preparados" -ForegroundColor Yellow
Write-Host "🔑 Te pedirá la contraseña de PostgreSQL..." -ForegroundColor Yellow
Write-Host ""

# Ejecutar comandos
& $psqlPath -U postgres -d postgres -f $tempFile

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ Base de datos configurada exitosamente!" -ForegroundColor Green
    
    # Limpiar archivo temporal
    Remove-Item $tempFile -Force -ErrorAction SilentlyContinue
    
    Write-Host "🚀 Ahora ejecutando migraciones de Laravel..." -ForegroundColor Yellow
    
    # Ejecutar migraciones
    php artisan migrate --force
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "🎉 ¡TODO CONFIGURADO CORRECTAMENTE!" -ForegroundColor Green
        Write-Host ""
        Write-Host "📊 Información de la base de datos:" -ForegroundColor Cyan
        Write-Host "   • Base de datos: laravel_auth" -ForegroundColor White
        Write-Host "   • Usuario: laravel_user" -ForegroundColor White  
        Write-Host "   • Contraseña: LaRappa501_Password" -ForegroundColor White
        Write-Host "   • Host: 127.0.0.1:5432" -ForegroundColor White
        Write-Host ""
        Write-Host "✨ Tu aplicación Laravel ya está usando PostgreSQL!" -ForegroundColor Green
        
    } else {
        Write-Host "❌ Error al ejecutar migraciones" -ForegroundColor Red
        Write-Host "🔍 Verifica la configuración en .env" -ForegroundColor Yellow
    }
    
} else {
    Write-Host ""
    Write-Host "Error al configurar la base de datos" -ForegroundColor Red
    Write-Host "Verifica que la contraseña sea correcta" -ForegroundColor Yellow
    Remove-Item $tempFile -Force -ErrorAction SilentlyContinue
}