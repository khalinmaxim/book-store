FROM php:8.2-fpm-alpine

# Установка системных зависимостей
RUN apk update && apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git

# Установка PHP расширений
RUN docker-php-ext-install pdo pdo_pgsql zip

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создание рабочей директории
WORKDIR /var/www/html

# Копирование файлов приложения
COPY . .

# Установка прав
RUN chown -R www-data:www-data /var/www/html

# Открытие порта
EXPOSE 9000

CMD ["php-fpm"]
