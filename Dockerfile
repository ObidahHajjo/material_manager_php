# Use the official PHP image
FROM php:8.2-apache

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy app files into container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/public

# Replace the default document root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Optional: Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Optional: Expose port
EXPOSE 80
