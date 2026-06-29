#!/bin/sh
set -e

echo "================================="
echo "=== Rekod container starting ==="
echo "================================="

echo "--- Environment Variables ---"
echo "APP_ENV=[$APP_ENV]"
echo "APP_DEBUG=[$APP_DEBUG]"
echo "APP_URL=[$APP_URL]"
echo "DB_CONNECTION=[$DB_CONNECTION]"
echo "DB_HOST=[$DB_HOST]"
echo "DB_PORT=[$DB_PORT]"
echo "DB_DATABASE=[$DB_DATABASE]"
echo "DB_USERNAME=[$DB_USERNAME]"

echo "--- Checking .env files in container ---"
ls -la .env* || true

echo "--- DB_HOST Raw Bytes ---"
printf 'DB_HOST raw bytes: '
printf '%s' "$DB_HOST" | od -An -t x1
echo

echo "--- DNS Test ---"
getent hosts "$DB_HOST" || true

echo "--- Ping Test ---"
ping -c 1 "$DB_HOST" || true

echo "--- Clearing Laravel caches ---"
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "--- Laravel about ---"
php artisan about || true

echo "--- Running migrations ---"
php artisan migrate --force || true

echo "--- Caching config ---"
php artisan config:cache || true

echo "--- Caching routes ---"
php artisan route:cache || true

echo "--- Caching views ---"
php artisan view:cache || true

echo "--- Storage link ---"
php artisan storage:link || true

echo "================================="
echo "=== Starting PHP server on port ${PORT:-80} ==="
echo "================================="

exec php -S 0.0.0.0:${PORT:-80} \
    -t public \
    public/index.php