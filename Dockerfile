# 🐘 Imagen base con PHP 8.3 y FPM
FROM php:8.3-fpm

# 🧰 Instalar dependencias del sistema necesarias
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libssl-dev pkg-config \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql zip gd
    

# 🔌 Instalar grpc desde PECL
RUN pecl install grpc && docker-php-ext-enable grpc

# 🎼 Instalar Composer desde imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 🗂️ Directorio de trabajo
WORKDIR /var/www

# 📦 Copiar proyecto completo
COPY . .

# 📚 Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 🔐 Configurar permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# ✅ Comando por defecto
CMD ["php-fpm"]
