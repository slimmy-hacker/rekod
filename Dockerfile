FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev zip unzip nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# THE FIX: Add --no-autoloader to bypass the Route crash during build
RUN composer install --no-dev --no-scripts --no-interaction --ignore-platform-reqs

# Re-generate optimized autoload AFTER the file is in place
RUN composer dump-autoload --optimize --no-scripts

# Create Nginx Config using a different method to avoid variable issues
RUN printf "server { \n\
    listen 80; \n\
    root /var/www/html/public; \n\
    index index.php; \n\
    location / { \n\
        try_files \ \/ /index.php?\; \n\
    } \n\
    location ~ \.php$ { \n\
        include fastcgi_params; \n\
        fastcgi_pass 127.0.0.1:9000; \n\
        fastcgi_param SCRIPT_FILENAME \\; \n\
    } \n\
}" > /etc/nginx/sites-available/default

RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

RUN chmod +x /var/www/html/start.sh

EXPOSE 80
CMD ["/var/www/html/start.sh"]
