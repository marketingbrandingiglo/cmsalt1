# Runs the Laravel + Filament CMS on Railway. Railway's Railpack builder
# can't reliably auto-detect this app (Laravel in a subdirectory), so this
# Dockerfile takes over the build deterministically.
# composer.lock has symfony/console pinned to a version requiring PHP >=8.4.1
# (this project targets PHP 8.5 per CLAUDE.md), so the image must be 8.4+.
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpq-dev libsqlite3-dev libonig-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

EXPOSE 8080
CMD php artisan storage:link --force && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
