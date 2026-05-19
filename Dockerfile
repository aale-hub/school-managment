FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libxml2-dev \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

RUN php database/migrate.php

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
