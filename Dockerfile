FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Install PHP dependencies WITHOUT scripts (prevents artisan crash before .env exists)
RUN composer install --no-interaction --prefer-dist --no-scripts --optimize-autoloader

# Install Node 20 (required by vite@7 and laravel-vite-plugin@2 — Node 18 fails EBADENGINE)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Remove any stale node_modules copied in from the host (Windows perms break the vite binary)
# and install fresh inside the container, explicitly fixing executable bits before building
RUN rm -rf node_modules \
    && npm install \
    && chmod -R +x node_modules/.bin \
    && npm run build

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
