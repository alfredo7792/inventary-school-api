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

# Configura las variables de entorno
ENV APP_ENV=production
ENV APP_KEY=base64:90tXpRY07sYsEElESgzBzPqa8ZcbjB/eCnfYgQD7+NE=
ENV APP_DEBUG=false
ENV APP_URL=http://tu_dominio # Reemplaza con tu dominio
ENV DB_CONNECTION=sqlite
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

# Habilita la extensión de PHP para PostgreSQL
RUN docker-php-ext-install pdo_pgsql

# Habilita la extensión de PHP para zip
RUN docker-php-ext-install zip

# Habilita la extensión de PHP para GD
RUN docker-php-ext-install gd

# Copia el archivo .env
COPY .env .env

# Ejecuta las migraciones
RUN php artisan migrate

# Exponer el puerto 80
EXPOSE 80

# Define el comando para ejecutar el servidor web
CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]