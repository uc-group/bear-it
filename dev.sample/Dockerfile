FROM php:8.0-apache-buster AS php-build
VOLUME /var/www/bear-it
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update && apt-get -y install zlib1g-dev libzip-dev zip curl acl vim nano && a2enmod rewrite
RUN docker-php-ext-install zip && pecl install xdebug-3.0.4 && docker-php-ext-enable xdebug
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN useradd bear-it
WORKDIR /var/www/bear-it
