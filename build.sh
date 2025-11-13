#!/usr/bin/env bash
# exit on error
set -o errexit

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm ci
npm run build

# Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
