FROM php:8.2.9-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip curl gnupg libpng-dev libonig-dev libxml2-dev openjdk-17-jre \
    && docker-php-ext-install pdo_mysql mbstring gd xml bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

WORKDIR /var/www

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
