# Versión PHP compatible con tu Laragon
FROM php:8.2-fpm

# Instalar dependencias específicas para Windows compatibility
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libssl-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl soap sockets \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configurar PHP para Windows
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/custom.ini

# Crear directorios para Windows compatibility
RUN mkdir -p /var/www/storage/app/comprobantes/{autorizados,enviados,firmados,no_firmados,pdfs,xmlaprobados,no_autorizados,devueltos,no_enviados}
RUN mkdir -p /var/www/storage/{framework,logs}

# Copiar todo el proyecto
COPY . /var/www

# Permisos para Windows
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage

WORKDIR /var/www
