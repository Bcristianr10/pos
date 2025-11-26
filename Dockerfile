FROM php:8.4-fpm

# ----------------------------------------
# 1. Instalar dependencias del sistema
# ----------------------------------------
RUN apt-get update && apt-get install -y \
    zip unzip curl git \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring exif pcntl bcmath \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ----------------------------------------
# 2. Instalar composer
# ----------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ----------------------------------------
# 3. Configurar bash (opcional)
# ----------------------------------------
RUN echo 'export PS1="root@kash-x:\\w# "' >> /root/.bashrc

WORKDIR /var/www

# ----------------------------------------
# 4. Copiar archivos del proyecto
# ----------------------------------------
COPY . .

# ----------------------------------------
# 5. Ajustar permisos (Laravel)
# ----------------------------------------
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# ----------------------------------------
# 6. Iniciar PHP-FPM
# ----------------------------------------
CMD ["php-fpm"]
