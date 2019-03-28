FROM php:7.2-apache-stretch AS php-build
VOLUME /var/www/bear-it
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update && apt-get -y install zlib1g-dev zip curl acl vim && a2enmod rewrite
RUN docker-php-ext-install zip
COPY --from=composer:1.7 /usr/bin/composer /usr/bin/composer
RUN useradd bear-it
WORKDIR /var/www/bear-it
