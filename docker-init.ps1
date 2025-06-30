# Script de inicialización para Windows
Write-Host "Iniciando configuración del proyecto..." -ForegroundColor Green

# Verificar Docker
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "Docker no está instalado. Por favor instale Docker Desktop primero." -ForegroundColor Red
    exit 1
}

# Construir imágenes
Write-Host "Construyendo imágenes Docker..." -ForegroundColor Yellow
docker-compose build

# Iniciar contenedores
Write-Host "Iniciando contenedores..." -ForegroundColor Yellow
docker-compose up -d

# Instalar dependencias
Write-Host "Instalando dependencias de Composer..." -ForegroundColor Yellow
docker-compose exec app composer install

# Configurar .env
if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "Archivo .env creado desde .env.example" -ForegroundColor Yellow
}

# Generar clave de aplicación
Write-Host "Generando clave de aplicación..." -ForegroundColor Yellow
docker-compose exec app php artisan key:generate

# Ejecutar migraciones y seeders
Write-Host "Ejecutando migraciones y seeders..." -ForegroundColor Yellow
docker-compose exec app php artisan migrate --seed

# Configurar storage
Write-Host "Configurando permisos de storage..." -ForegroundColor Yellow
docker-compose exec app chmod -R 775 storage

Write-Host "¡Configuración completada con éxito!" -ForegroundColor Green
Write-Host "La aplicación está disponible en: http://localhost:8080" -ForegroundColor Cyan
