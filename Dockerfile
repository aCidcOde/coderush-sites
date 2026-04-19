FROM php:8.3-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache git unzip \
 && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader --no-progress \
 && chown -R www-data:www-data /var/www/html

EXPOSE 9000
