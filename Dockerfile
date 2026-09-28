FROM php:8.3-apache

# Apache mods
RUN a2enmod rewrite headers

# PHP extensions
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli mbstring exif pcntl gd xml

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# System deps + node/npm
RUN apt-get update && \
    apt-get install -y git curl zip unzip nodejs npm && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Composer deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# App source + assets
COPY . .
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
