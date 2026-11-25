#!/bin/sh
set -eu

app_port="${PORT:-80}"
export DB_CONNECTION=mysql

if [ -z "${DB_URL:-}" ]; then
    : "${DB_HOST:?Set DB_HOST or DB_URL to your TiDB Cloud connection}"
    : "${DB_DATABASE:?Set DB_DATABASE or DB_URL to your TiDB Cloud connection}"
    : "${DB_USERNAME:?Set DB_USERNAME or DB_URL to your TiDB Cloud connection}"
    : "${DB_PASSWORD:?Set DB_PASSWORD or DB_URL to your TiDB Cloud connection}"
fi

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
php artisan storage:link --force
chown -R www-data:www-data storage bootstrap/cache public/storage

sed -i "s/^Listen 80$/Listen ${app_port}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${app_port}>/" /etc/apache2/sites-available/000-default.conf

attempt=1
until php artisan migrate --force; do
    if [ "$attempt" -ge 10 ]; then
        echo "Database migrations failed after 10 attempts; check the TiDB Cloud connection and migration error above." >&2
        exit 1
    fi

    echo "TiDB is not ready yet; retrying migrations in 3 seconds (attempt $((attempt + 1))/10)." >&2
    sleep 3
    attempt=$((attempt + 1))
done

php artisan optimize
exec apache2-foreground
