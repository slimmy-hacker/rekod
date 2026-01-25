FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Install PHP extensions for MySQL and Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory and copy code
WORKDIR /var/www/html
COPY . /var/www/html

# Install dependencies (skip scripts to avoid the Student.php error)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Configure Nginx for Laravel
RUN echo 'server { \
    listen 80; \
    root /var/www/html/public; \
    index index.php; \
    location / { try_files  / /index.php?; } \
    location ~ \.php$ { \
        include fastcgi_params; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME ; \
    } \
}' > /etc/nginx/sites-available/default

# Give permissions to the start script
RUN chmod +x /var/www/html/start.sh

EXPOSE 80

CMD ["/var/www/html/start.sh"]
