FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev xxd

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# === DIAGNOSTIC: runs during BUILD so we can see it in build logs ===
RUN echo "=== BUILD DIAGNOSTIC ===" \
    && echo "bootstrap/app.php first bytes:" \
    && xxd bootstrap/app.php | head -3 \
    && echo "PHP syntax check:" \
    && php -l bootstrap/app.php \
    && echo "withMiddleware check:" \
    && grep -n "withMiddleware\|alias" bootstrap/app.php \
    && echo "=== END DIAGNOSTIC ==="

# Install PHP dependencies WITHOUT scripts
RUN composer install --no-interaction --prefer-dist --no-scripts --optimize-autoloader

# Install Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Build frontend assets
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

