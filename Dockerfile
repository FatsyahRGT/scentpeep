# ==========================================
# Stage 1: Vendor & Dependencies Builder
# ==========================================
FROM composer:2.7 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ==========================================
# Stage 2: Production PHP-FPM Application
# ==========================================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install system runtime libraries & PHP extension dependencies
RUN apk update && apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client \
    sqlite \
    sqlite-dev \
    bash

# Install required PHP extensions for Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    bcmath \
    opcache \
    intl \
    zip

# Configure PHP production opcache & security hardening (PHP2Shell mitigation)
RUN { \
    echo 'expose_php=Off'; \
    echo 'session.upload_progress.enabled=0'; \
    echo 'upload_max_filesize=32M'; \
    echo 'post_max_size=32M'; \
    echo 'memory_limit=256M'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
} > /usr/local/etc/php/conf.d/laravel-security.ini

# Copy application source code
COPY . /var/www/html

# Copy vendor packages from builder stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy entrypoint script and set permissions
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set directory permissions for web server user
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
