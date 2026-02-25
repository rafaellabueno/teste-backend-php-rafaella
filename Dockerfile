FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

EXPOSE 8000

CMD sh -c "if [ ! -f .env ]; then cp .env.example .env; fi && \
           php artisan key:generate --force && \
           php artisan migrate --force && \
           php artisan serve --host=0.0.0.0 --port=8000"