# FROM php:7.3.13-alpine

# RUN apk add --no-cache \
#     wget \
#     curl \
#     git \
#     shadow \
#     build-base \
#     autoconf \
#     hiredis \
#     libxml2-dev \
#     zlib-dev \
#     libevent \
#     libevent-dev \
#     openssl-dev \
#     gmp-dev \
#     icu-dev \
#     && \
#     docker-php-ext-install \
#         pcntl \
#         mbstring \
#         pdo \
#         pdo_mysql \
#         tokenizer \
#         xml \
#         sockets \
#         gmp \
#         bcmath \
#         intl \
#         && \
#     # Libevent
#     pecl install event && \
#     # phpiredis
#     apk add --no-cache --virtual .build-deps \
#         hiredis-dev \
#         && \
#     git clone https://github.com/francislavoie/phpiredis.git && \
#     ( \
#         cd phpiredis && \
#         phpize && ./configure --enable-phpiredis && \
#         make && make install && \
#         docker-php-ext-enable phpiredis \
#     ) && \
#     rm -rf phpiredis && \
#     # Cleanup
#     apk del .build-deps && \
#     rm -rf /var/cache/apk/* && \
#     mkdir -p /var/www/html

# # Add non-root user, prevents files being created with root permissions on host machine
# ARG PUID=1000
# ENV PUID ${PUID}
# ARG PGID=1000
# ENV PGID ${PGID}
# RUN groupmod -g ${PGID} www-data && \
#     usermod -u ${PUID} www-data

# USER www-data

# WORKDIR /var/www/html

# EXPOSE 6001

# CMD ["php", "artisan", "websockets:serve"]
# # CMD ["php artisan queue:work"]






FROM php:8.1.13-fpm-alpine
# FROM php:7.4-fpm-alpine
# Arguments defined in docker-compose.yml
ARG user
ARG uid

LABEL Description="Base setup for IAmsterdam survey portal development."

RUN apk add --update --no-cache \
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
    && pecl install redis xdebug-3.1.6 \
    && docker-php-ext-enable redis xdebug \
    && apk del $PHPIZE_DEPS


# RUN addgroup -S -g $uid $user \
#     && adduser -S -D -u $uid -h /home/$user -G www-data $user

# RUN mkdir -p /home/$user/.composer && \
#     chown -R $user:$user /home/$user

# # CMD ["/usr/local/bin/start"]

# WORKDIR /var/www/html



# USER $user

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

EXPOSE 6001

CMD ["php", "artisan", "websockets:serve"]
# CMD ["php artisan queue:work"]



