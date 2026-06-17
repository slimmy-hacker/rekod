#!/bin/sh
set -e

echo "=== Rekod container starting ==="
echo "=== DEBUG: CLOUDINARY_URL raw value below (between markers) ==="
echo "[START]${CLOUDINARY_URL}[END]"
echo "=== DEBUG: end ==="
echo "=== DEBUG: checking for .env file in container ==="
ls -la /var/www/.env* 2>&1 || echo "no .env files found"
if [ -f /var/www/.env ]; then
  echo "=== DEBUG: .env file CONTENTS (CLOUDINARY line only) ==="
  grep -n CLOUDINARY /var/www/.env || echo "no CLOUDINARY line in .env"
fi
echo "=== DEBUG: end file check ==="

# Cache config now that real env vars are injected by Render
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations automatically on every boot (safe — only applies new ones)
php artisan migrate --force

# Ensure public storage symlink exists (skip if using Cloudinary exclusively)
php artisan storage:link || true

echo "=== Starting server on port ${PORT:-80} ==="

# Render injects $PORT — bind to it, default to 80 for local testing
php artisan serve --host=0.0.0.0 --port=${PORT:-80}
