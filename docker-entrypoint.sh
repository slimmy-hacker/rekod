#!/bin/sh
set -e

echo "================================="
echo "=== Rekod container starting ==="
echo "================================="

echo "--- Environment Variables ---"
echo "APP_ENV=$APP_ENV"
echo "APP_DEBUG=$APP_DEBUG"
echo "APP_URL=$APP_URL"
echo "DB_CONNECTION=$DB_CONNECTION"
echo "DB_HOST=$DB_HOST"
echo "DB_PORT=$DB_PORT"
echo "DB_DATABASE=$DB_DATABASE"
echo "DB_USERNAME=$DB_USERNAME"

echo "--- Clearing Laravel caches ---"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "--- Laravel about ---"
php artisan about

echo "--- Running migrations ---"
php artisan migrate --force

echo "--- Caching config ---"
php artisan config:cache

echo "--- Caching routes ---"
php artisan route:cache

echo "--- Caching views ---"
php artisan view:cache

echo "--- Storage link ---"
php artisan storage:link || true

echo "================================="
echo "=== Starting PHP server on port ${PORT:-80} ==="
echo "================================="

exec php -S 0.0.0.0:${PORT:-80} \
    -t public \
    public/index.php