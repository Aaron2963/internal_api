FROM php:7.4-apache

RUN apt-get update -y && apt-get install -y openssl zip unzip git libpng-dev libxml2-dev libzip-dev
RUN docker-php-ext-install mysqli pdo pdo_mysql gd fileinfo iconv xml zip
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN a2enmod rewrite

EXPOSE 80

WORKDIR /var/www/html

CMD [ "bash", "/var/www/html/example/startup.sh" ]