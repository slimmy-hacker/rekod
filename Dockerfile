FROM richarvey/nginx-php-fpm:php8.3

# Copy the application code
COPY . /var/www/html

# Set the webroot to Laravel's public folder
ENV WEBROOT /var/www/html/public
ENV APP_KEY_QUOTED 1

# Install dependencies without running scripts (prevents Student.php error)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port
EXPOSE 80
