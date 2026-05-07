# =============================================================================
# STAGE 1 — Frontend (Node Alpine, solo para compilar assets)
# =============================================================================
FROM docker.io/library/node:20-alpine AS frontend

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

# =============================================================================
# STAGE 2 — PHP Alpine (imagen final, ~150 MB vs ~800 MB anterior)
# =============================================================================
FROM docker.io/library/php:8.2-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

# Zona horaria México
ENV TZ=America/Mexico_City
RUN apk add --no-cache tzdata \
    && cp /usr/share/zoneinfo/America/Mexico_City /etc/localtime \
    && echo "America/Mexico_City" > /etc/timezone \
    && apk del tzdata

# Extensiones PHP estrictamente necesarias para Laravel + MySQL
# pdo_mysql  → conexión a MySQL
# mbstring   → manejo de strings UTF-8
# zip        → descompresión de assets/packages
# opcache    → caché de bytecode (rendimiento)
RUN apk add --no-cache \
        libzip-dev \
        oniguruma-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        opcache \
        gd \
    && rm -rf /tmp/*

# Composer directo desde su imagen oficial (sin instalar git/curl en la final)
COPY --from=docker.io/library/composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencias PHP (solo producción — sin paquetes de testing/dev)
COPY composer.json composer.lock ./
RUN composer install \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
        --no-autoloader \
        --no-dev

# Código fuente + assets compilados
COPY . .
COPY --from=frontend /app/public/build ./public/build
COPY --from=frontend /app/public/build /opt/frangy/public-build

# Autoloader optimizado + .env desde ejemplo si no existe
RUN rm -f bootstrap/cache/*.php \
    && if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi \
    && composer dump-autoload --optimize --no-interaction \
    && (php artisan vendor:publish --tag=laravel-assets --ansi --force 2>/dev/null || true) \
    && rm -f bootstrap/cache/*.php

# Permisos para PHP-FPM
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# OPcache afinado para producción/desarrollo en contenedor
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.interned_strings_buffer=8"; \
    echo "opcache.max_accelerated_files=4000"; \
    echo "opcache.validate_timestamps=1"; \
    echo "opcache.revalidate_freq=0"; \
    echo "opcache.fast_shutdown=1"; \
} > /usr/local/etc/php/conf.d/opcache.ini

EXPOSE 9000
CMD ["php-fpm"]
