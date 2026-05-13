#!/bin/bash

# Exit on error
set -e

# Create necessary Nginx directories for Alpine
mkdir -p /run/nginx

# Ensure correct permissions for storage and cache directories
# Note: Since the container runs as root by default, we apply these safely
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Run Laravel artisan commands (Automatic Migration and Caching)
echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache

# Start PHP-FPM in the background
php-fpm -D

# Start Nginx in the foreground
nginx -g "daemon off;"
