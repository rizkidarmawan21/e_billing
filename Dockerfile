FROM php:8.3-apache

# Apache mods
RUN a2enmod rewrite headers

# System deps + PHP extension deps
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git curl zip unzip nodejs npm libonig-dev libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Composer deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli mbstring exif pcntl gd xml && \
    docker-php-ext-enable mbstring

# App source + assets
COPY . .
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
