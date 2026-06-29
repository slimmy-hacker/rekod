#!/bin/sh
set -e

echo "=== Rekod container starting ==="

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
php artisan storage:link 2>/dev/null || true

echo "=== Starting PHP server on port ${PORT:-80} ==="
php -S 0.0.0.0:${PORT:-80} -t /var/www/public /var/www/public/index.php

