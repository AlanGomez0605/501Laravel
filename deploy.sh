#!/bin/bash

# Script de deployment para Hostinger
echo "=== Iniciando deployment de Laravel ==="

# 1. Optimizar para producción
echo "Optimizando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Ejecutar migraciones en producción
echo "Ejecutando migraciones..."
php artisan migrate --force

# 3. Limpiar cache anterior
echo "Limpiando cache..."
php artisan cache:clear

# 4. Crear storage link
echo "Creando enlace de storage..."
php artisan storage:link

echo "=== Deployment completado ==="