FROM php:8.4-fpm

# Install dependencies sistem, ekstensi PHP, dan Nginx
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Konfigurasi Nginx agar menggunakan TCP port PHP-FPM
RUN rm /etc/nginx/sites-enabled/default
COPY <<EOF /etc/nginx/sites-available/default
server {
    listen 8080;
    index index.php index.html;
    root /app/public;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOF
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Pengaturan Env Composer untuk mencegah Timeout (HTTP 504) & Izin Superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HTTP_TIMEOUT=600

WORKDIR /app
COPY . .

# Install dependency Laravel dengan fallback koneksi jika dist GitHub timeout
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs || \
    composer install --no-dev --optimize-autoloader --ignore-platform-reqs --prefer-source

# Set permission storage
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8080

# Jalankan php-fpm di background dan Nginx di foreground
CMD ["sh", "-c", "php-fpm -F -R & nginx -g 'daemon off;'"]