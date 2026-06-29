#!/bin/sh
set -e

echo "=== Rekod container starting ==="

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan about

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:link || true

php -S 0.0.0.0:${PORT:-80} -t public public/index.php