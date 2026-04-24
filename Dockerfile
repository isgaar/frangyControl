FROM docker.io/library/node:20-bookworm-slim AS frontend

WORKDIR /app

COPY package*.json ./
RUN if [ -f package-lock.json ]; then \
        npm ci --include=dev --no-audit --no-fund; \
    else \
        npm install --include=dev --no-audit --no-fund; \
    fi

COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM docker.io/library/php:8.2-fpm

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libicu-dev \
    zip unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip xml intl

COPY --from=docker.io/library/composer:2 /usr/bin/composer /usr/bin/composer

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi \
    && composer dump-autoload --optimize --no-interaction \
    && php artisan vendor:publish --tag=laravel-assets --ansi --force

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
