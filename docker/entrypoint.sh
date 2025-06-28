#!/bin/bash

# Crear carpetas de comprobantes
mkdir -p storage/app/comprobantes/{autorizados,enviados,firmados,no_firmados,pdfs,xmlaprobados,no_autorizados,devueltos,no_enviados}

# Instalar dependencias
composer install
npm install
npm run build

# Limpiar cache, migrar y seed
php artisan config:clear
php artisan migrate --seed --force

# Iniciar PHP-FPM
exec php-fpm

