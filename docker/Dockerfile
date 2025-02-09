FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libssl-dev \
    libpq-dev \
    nano \
    libonig-dev \
    && docker-php-ext-install pdo pdo_pgsql bcmath \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY xdebug.ini "${PHP_INI_DIR}/conf.d/"

WORKDIR /var/www/html

RUN mkdir -p var/log && \
    chown -R www-data:www-data var && \
    chmod -R 775 var

CMD ["php-fpm"]