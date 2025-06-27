# Use official PHP image with Apache
FROM php:8.2-apache

# Copy all files to Apache's web root
COPY . /var/www/html/

# Expose port (Render uses 10000 internally, but Apache uses 80 here)
EXPOSE 80

# Enable Apache rewrite module if needed (optional)
RUN a2enmod rewrite
