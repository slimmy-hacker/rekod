#!/bin/sh
set -e

echo "=== Rekod container starting ==="
echo "=== PHP version: $(php --version | head -1) ==="
echo "=== APP_KEY set: $([ -n "$APP_KEY" ] && echo YES || echo NO) ==="
echo "=== DB_HOST: $DB_HOST ==="

echo "--- Step 1: config:clear ---"
php artisan config:clear || echo "config:clear FAILED"

echo "--- Step 2: route:clear ---"
php artisan route:clear || echo "route:clear FAILED"

echo "--- Step 3: view:clear ---"
php artisan view:clear || echo "view:clear FAILED"

echo "--- Step 4: cache:clear ---"
php artisan cache:clear || echo "cache:clear FAILED"

echo "--- Step 5: migrate ---"
php artisan migrate --force || echo "migrate FAILED"

echo "--- Step 6: storage:link ---"
php artisan storage:link || true

echo "--- Step 7: config:cache ---"
php artisan config:cache || echo "config:cache FAILED"

echo "--- Step 8: route:cache ---"
php artisan route:cache || echo "route:cache FAILED"

echo "--- Step 9: view:cache ---"
php artisan view:cache || echo "view:cache FAILED"

echo "=== Starting server on port ${PORT:-80} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-80}
