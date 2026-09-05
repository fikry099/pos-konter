FROM php:8.4-fpm

# Install dependencies sistem, ekstensi PHP, dan Nginx
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev zip curl nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Konfigurasi Nginx untuk Laravel pada port 8080
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
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    }
}
EOF
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Buat folder run untuk socket PHP-FPM
RUN mkdir -p /var/run/php

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Set permission storage
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8080

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD php-fpm -D && nginx -g "daemon off;"