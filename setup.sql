-- Crear usuario Laravel
CREATE USER laravel_user WITH PASSWORD 'LaRappa501_Password';

-- Crear base de datos
CREATE DATABASE laravel_auth OWNER laravel_user;

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON DATABASE laravel_auth TO laravel_user;