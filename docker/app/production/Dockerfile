FROM php:8.4-fpm

# Update package list and install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    mariadb-client \
    libmariadb-dev \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Install required packages
RUN docker-php-ext-install pdo pdo_mysql gd bcmath zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Copy php.ini
COPY ./docker/app/production/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html

# Copy the codebase
COPY . ./

# Run composer install for production and give permissions
RUN sed 's_@php artisan package:discover_/bin/true_;' -i composer.json \
    && composer install --ignore-platform-req=php --no-dev --optimize-autoloader \
    && composer clear-cache \
    && php artisan package:discover --ansi \
    && chmod -R 775 storage \
    && chown -R www-data:www-data storage \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache

# Copy entrypoint
COPY ./docker/app/production/start-container /usr/local/bin/start-container

# Give permisisons to everything in bin/
RUN chmod a+x /usr/local/bin/*

ENTRYPOINT ["/usr/local/bin/start-container"]

CMD ["php-fpm"]