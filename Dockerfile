FROM php:8.2.8-fpm-alpine

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN set -ex \
    && apk --no-cache add postgresql-dev yarn\
    && docker-php-ext-install pdo pdo_pgsql

COPY . /var/www

RUN php artisan optimize

RUN php artisan key:generate

RUN php artisan config:clear

RUN php artisan config:cache

EXPOSE 80

CMD ["php-fpm"]