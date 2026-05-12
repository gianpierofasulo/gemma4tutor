FROM php:8.2-fpm

# Installazione dipendenze di sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Pulizia cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installazione estensioni PHP necessarie per Laravel e MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Installazione Composer (il gestore pacchetti di Laravel)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Settaggio della cartella di lavoro
WORKDIR /var/www/html

# Assicura che le cartelle esistano e siano scrivibili
RUN mkdir -p /var/www/html/storage/framework/cache/data \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/app/public \
    && chown -R www-data:www-data /var/www/html/storage

EXPOSE 9000