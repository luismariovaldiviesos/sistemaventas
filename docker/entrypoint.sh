#!/bin/bash
set -e
cd /var/www

if [ ! -f ".env" ]; then
  cp .env.example .env
  php artisan key:generate
fi

if [ ! -d "vendor" ]; then
  composer install --no-interaction --prefer-dist
fi

if [ ! -d "node_modules" ]; then
  npm install
  npm run dev
fi

mkdir -p storage/app/comprobantes/{autorizados,enviados,firmados,no_firmados,pdfs,xmlaprobados,no_autorizados,devueltos,no_enviados}

until mysqladmin ping -h"$DB_HOST" --silent; do
  echo "⏳ Esperando a MySQL…"
  sleep 2
done

php artisan migrate --seed --force

exec "$@"
