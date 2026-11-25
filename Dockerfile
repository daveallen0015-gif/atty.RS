FROM php:8.4-apache

# System libraries and PHP extensions used by Laravel and the image uploads.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        opcache \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
    && a2enmod rewrite headers expires deflate \
    && rm -rf /var/lib/apt/lists/*

# Production PHP settings, including OPcache.
COPY docker/php-production.ini /usr/local/etc/php/conf.d/99-law-production.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# .dockerignore excludes local secrets, vendor, and editor files.
COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

EXPOSE 80

# Apply migrations before starting Apache; Render supplies PORT at runtime.
CMD ["sh", "-c", "set -eu; app_port=\"${PORT:-80}\"; mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache; php artisan storage:link --force; chown -R www-data:www-data storage bootstrap/cache public/storage; sed -i \"s/^Listen 80$/Listen ${app_port}/\" /etc/apache2/ports.conf; sed -i \"s/<VirtualHost \\*:80>/<VirtualHost *:${app_port}>/\" /etc/apache2/sites-available/000-default.conf; php artisan migrate --force; php artisan optimize; exec apache2-foreground"]
