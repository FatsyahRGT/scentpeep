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

# Clear stale caches so fresh .env variables are always loaded
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Auto-generate APP_KEY if empty in environment
if [ -z "$APP_KEY" ]; then
    echo "Generating Application Key..."
    GENERATED_KEY=$(php artisan key:generate --show)
    export APP_KEY="$GENERATED_KEY"
    echo "Application Key initialized successfully."
fi

# Run database migrations automatically
echo "Running database migrations..."
php artisan migrate --force || echo "Database migration warning: will retry on next startup."

echo "Starting PHP-FPM server..."
exec "$@"
