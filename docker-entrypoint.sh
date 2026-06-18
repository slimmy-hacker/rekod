#!/bin/sh
set -e

echo "=== Rekod container starting ==="

# Clear any stale caches from the build phase first
# This lets Laravel boot from raw config files using Render's env vars
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run migrations now that app is booting cleanly from env vars
php artisan migrate --force

# Storage symlink
php artisan storage:link || true

# Now rebuild caches with correct runtime values
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting server on port ${PORT:-80} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-80}
