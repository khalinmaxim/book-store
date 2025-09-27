FROM php:8.2-apache

ENV TZ=Europe/Warsaw

# Системные пакеты и PHP-расширения
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev git unzip cron msmtp ca-certificates \
 && docker-php-ext-install pdo pdo_pgsql \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

# Composer (копируем бинарник из официального образа)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Предустановка зависимостей на этапе сборки (на старте контейнера тоже проверяем в docker-compose)
COPY ./src/composer.json /var/www/html/
RUN composer install --no-interaction --prefer-dist || true

# Настройка почты через msmtp для функции mail()/PHPMailer
RUN printf "defaults\naccount default\nhost mailhog\nport 1025\nauto_from on\nfrom no-reply@bookstore.local\n" > /etc/msmtprc \
 && chmod 600 /etc/msmtprc \
 && echo 'sendmail_path = "/usr/bin/msmtp -t"' > /usr/local/etc/php/conf.d/sendmail.ini

# Разрешить .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
