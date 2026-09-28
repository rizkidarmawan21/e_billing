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

# --- Apache: arahkan DocumentRoot ke /public (entrypoint Laravel) ---
# Tanpa ini Apache serve /var/www/html -> 403 Forbidden di root domain.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Entrypoint: tunggu DB, generate APP_KEY, migrate, cache
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
