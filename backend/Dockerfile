# Use PHP with Apache as the base image
FROM php:8.2-apache as web

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    mc

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install the GD extension
# The --with-freetype and --with-jpeg flags enable specific GD features
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Configure Apache DocumentRoot to point to Laravel's public directory
# and update Apache configuration files
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf


# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/storage/logs /var/www/html/bootstrap/cache /var/www/html/storage/framework

RUN php artisan package:discover

# Install project dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev


RUN php artisan key:generate
RUN php artisan config:clear
RUN php artisan config:cache
