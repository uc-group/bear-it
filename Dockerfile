FROM php:7.2 AS php-build
WORKDIR /usr/src/bear-it
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update && apt-get -y install zlib1g-dev zip curl
RUN docker-php-ext-install zip
COPY composer.json composer.lock ./
COPY --from=composer:1.7 /usr/bin/composer /usr/bin/composer
RUN composer install  --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --optimize && composer run-script post-install-cmd

FROM node:10 AS assets-build
WORKDIR /home/node/app
COPY assets assets
COPY package.json yarn.lock webpack.config.js ./
RUN mkdir -p /home/node/app/public && yarn install --no-cache && yarn build

FROM php:7.2-apache-stretch
WORKDIR /var/www/bear-it
RUN DEBIAN_FRONTEND=noninteractive apt-get -y update && apt-get -y install zlib1g-dev zip curl && a2enmod rewrite
RUN docker-php-ext-install zip
COPY config/apache2/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY --from=php-build /usr/src/bear-it/vendor vendor
COPY --from=assets-build /home/node/app/public/build public/build
COPY --from=assets-build /home/node/app/public/sw.js public/sw.js
COPY . .
RUN mkdir var && chown -R www-data:www-data var