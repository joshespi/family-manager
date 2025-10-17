
FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change document root folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copy composer files and install dependencies
COPY ./composer.json ./composer.lock ./
RUN composer install --no-interaction --prefer-dist

# Copy application code
COPY ./src ./src

# Copy custom php.ini configuration
COPY ./php.ini /usr/local/etc/php/


# Expose port 80
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]