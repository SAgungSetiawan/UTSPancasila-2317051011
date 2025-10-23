# Gunakan image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install dependencies untuk Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Aktifkan mod_rewrite Apache untuk Laravel
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy file Laravel ke container
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission
RUN chmod -R 777 storage bootstrap/cache

# Konfigurasi Apache agar .htaccess Laravel berfungsi
RUN echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
</Directory>" > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Expose port 80
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]
