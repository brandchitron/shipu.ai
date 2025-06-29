FROM php:8.2-apache

# Optional: Enable rewrite module (good for Laravel, routes)
RUN a2enmod rewrite

# Optional: Install common PHP extensions (if needed)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy source code
COPY . /var/www/html/

# Fix file permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html

EXPOSE 80
