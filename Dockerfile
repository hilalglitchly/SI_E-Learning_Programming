FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        pdo_sqlite \
        zip \
    && a2enmod rewrite headers deflate expires \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

RUN printf "<IfModule mod_deflate.c>\nAddOutputFilterByType DEFLATE text/html text/plain text/css application/javascript application/json image/svg+xml application/manifest+json\n</IfModule>\n<IfModule mod_expires.c>\nExpiresActive On\nExpiresByType text/css \"access plus 7 days\"\nExpiresByType application/javascript \"access plus 7 days\"\nExpiresByType image/png \"access plus 30 days\"\nExpiresByType image/svg+xml \"access plus 30 days\"\n</IfModule>\n" \
    > /etc/apache2/conf-enabled/perf.conf

RUN printf "expose_php = Off\nupload_max_filesize = 16M\npost_max_size = 20M\nmemory_limit = 256M\nopcache.enable = 1\nopcache.validate_timestamps = 0\n" \
    > /usr/local/etc/php/conf.d/app.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --optimize-autoloader

COPY . .

RUN mkdir -p storage/app/private/materi storage/app/private/tugas storage/app/private/submission \
        storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --optimize --no-interaction \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/render-start.sh /usr/local/bin/render-start.sh
RUN sed -i 's/\r$//' /usr/local/bin/render-start.sh \
    && chmod +x /usr/local/bin/render-start.sh

EXPOSE 10000

CMD ["/usr/local/bin/render-start.sh"]
