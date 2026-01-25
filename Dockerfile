FROM richarvey/nginx-php-fpm:latest
COPY . /var/www/html
WORKDIR /var/www/html
ENV SKIP_COMPOSER 0
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
RUN composer install --no-dev --optimize-autoloader
EXPOSE 80
