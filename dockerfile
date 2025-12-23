FROM php:8.3-cli

WORKDIR /app

# PHP dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev curl \
    && docker-php-ext-install pdo pdo_mysql zip

# Node.js (UNTUK BUILD TAILWIND)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY . .

# Install PHP deps
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets (INI KUNCI)
RUN npm install
RUN npm run build

# Permission
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=8080
