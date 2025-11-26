
FROM php:8.4-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev npm nodejs bash \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar dependencias para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cambiar prompt de root
RUN echo 'export PS1="root@kash-x:\\w# "' >> /root/.bashrc

WORKDIR /var/www

COPY . .

RUN composer install

RUN npm install

# Cambiar permisos (opcional, útil para Laravel)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

CMD ["php-fpm"]
