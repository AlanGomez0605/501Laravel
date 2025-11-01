@echo off
echo Iniciando servidor Laravel con PostgreSQL...
echo.

REM Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo Cache limpiado
echo.

REM Verificar conexión PostgreSQL
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'PostgreSQL: CONECTADO'; } catch(Exception $e) { echo 'PostgreSQL: ERROR - ' . $e->getMessage(); }"

echo.

REM Iniciar servidor
echo Iniciando servidor en http://127.0.0.1:8000
php artisan serve --host=127.0.0.1 --port=8000

pause