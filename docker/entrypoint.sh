#!/bin/sh
set -e

# Ensure permissions for Laravel storage and bootstrap cache
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    php artisan key:generate --force
fi

# Run database migrations automatically
echo "Running database migrations..."
php artisan migrate --force

# Optimize Laravel cache in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration and routes for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Starting PHP-FPM server..."
exec "$@"
