FROM php:8.4-cli

# Install dependencies sistem & ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy Composer dari official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Expose port Railway
EXPOSE 8080

# Jalankan server internal PHP
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}