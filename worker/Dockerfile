FROM php:8.1.13-fpm-alpine
# FROM php:7.4-fpm-alpine
# Arguments defined in docker-compose.yml
ARG user
ARG uid

LABEL Description="Base setup for Microservice."

# RUN apk add --update --no-cache \
RUN apk add --update \
    $PHPIZE_DEPS \
    freetype-dev \
    libpq-dev \
    libzip-dev \
    imagemagick-dev \
    pcre-dev \
    libevent \
    libevent-dev \
    openssl-dev \
    gmp-dev \
    icu-dev \
    && docker-php-ext-install pdo_mysql bcmath zip exif mysqli \
    # && docker-php-ext-configure gd --with-jpeg --with-freetype \
    # && docker-php-ext-install -j "$(nproc)" gd tokenizer sockets \
     && docker-php-ext-install -j "$(nproc)" gd  sockets \
          # Install sockets extension here
    && docker-php-ext-install sockets \
    && pecl install redis xdebug-3.1.6 \
    && docker-php-ext-enable redis xdebug \
    && apk del $PHPIZE_DEPS


# Add non-root user, prevents files being created with root permissions on host machine
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

# RUN addgroup -S -g ${PGID} www-data && \
#     usermod -u ${PUID} www-data

# USER www-data

RUN addgroup -S -g 1000 php \
    && adduser -S -D -u 1000 -h /home/php -G www-data 1000


WORKDIR /var/www/html

# EXPOSE 6001

CMD ["php", "artisan", "queue:work"]
# CMD ["php artisan queue:work"]



