# Utiliza una imagen base de PHP con FPM
FROM php:8.1-fpm

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia el archivo composer.json y composer.lock
COPY composer.json composer.lock ./

# Instala las dependencias usando Composer
RUN composer install --no-interaction --no-ansi

# Copia el resto del código fuente
COPY . .

# Instala las extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgd-dev \
    zlib1g-dev \
    libicu-dev \
    libmbstring-dev \
    libcurl4-openssl-dev

# Copia el archivo .env
COPY .env .env

# Exponer el puerto 80
EXPOSE 80

# Define el comando para ejecutar el servidor web
CMD ["php", "artisan", "serve"]
