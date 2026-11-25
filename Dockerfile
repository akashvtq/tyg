# There You Grow (TYG) — production image for Render (or any Docker host).
#
# This Laravel app is served from the project ROOT (index.php + /assets live at the top level,
# there is no public/ directory), so Apache is locked down to only expose index.php, /assets
# and robots.txt — never .env, vendor/, storage/ etc. See docker/apache.conf.

FROM php:8.2-apache

# PHP extensions Laravel + this app need (MySQL, images, QR codes, intl, zip)
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev \
        libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring bcmath intl zip gd exif opcache \
 && a2enmod rewrite headers expires \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP settings for production
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-tyg.ini

# Apache: our locked-down vhost, listening on $PORT (Render sets it; default 10000)
ENV PORT=10000
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN sed -ri 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
 && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

WORKDIR /var/www/html

# Install PHP dependencies first (better layer caching). There is no composer.lock in the repo,
# so versions are resolved here; commit a composer.lock to pin them.
# Scripts are skipped: artisan needs the database (AppServiceProvider reads settings at boot),
# so package discovery runs at container start instead (docker/start.sh).
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts \
 && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions \
             storage/framework/views storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache assets \
 && chmod +x docker/start.sh

EXPOSE 10000
CMD ["docker/start.sh"]
