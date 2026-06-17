#!/bin/bash
# ============================================================
# REKOD — Post-Deploy Setup Script
# Run these commands in the Render Shell after first deploy
# Render Dashboard → your service → Shell tab
# ============================================================

echo "=== Running Rekod post-deploy setup ==="

# 1. Run database migrations
echo ">> Migrating database..."
php artisan migrate --force

# 2. Seed the database (roles, default admin, etc.)
# Comment this out if you don't have seeders yet
# php artisan db:seed --force

# 3. Create storage symlink
echo ">> Creating storage symlink..."
php artisan storage:link

# 4. Clear and rebuild all caches
echo ">> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Create the sessions table (needed since SESSION_DRIVER=database)
echo ">> Creating sessions table..."
php artisan session:table
php artisan migrate --force

# 6. Create cache table (needed since CACHE_STORE=database)
echo ">> Creating cache table..."
php artisan cache:table
php artisan migrate --force

echo "=== Setup complete! Visit your Render URL to verify ==="
