FROM php:8.2-apache

# Enable mod_rewrite for PHP routing (if needed)
RUN a2enmod rewrite

# Copy all project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set permissions (optional)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
