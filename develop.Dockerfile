FROM php:8.1.13-fpm-alpine
# FROM php:7.4-fpm-alpine
# Arguments defined in docker-compose.yml
ARG user
ARG uid

LABEL Description="Base setup for Microservice Auth development."

# if user want to build without cache layer for docker
# RUN apk add --update --no-cache \
# if user want to build  cache layer for docker
RUN apk add --update \
    $PHPIZE_DEPS \
    git \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    libpq-dev \
    postgresql-dev \
    imagemagick-dev \
    pcre-dev \
    npm \
    nodejs \
    && docker-php-ext-install pdo_mysql bcmath zip exif mysqli pdo_pgsql \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install -j "$(nproc)" gd \
     # Install sockets extension here
    && docker-php-ext-install sockets \
    && pecl install redis xdebug-3.1.6 \
    && docker-php-ext-enable redis xdebug \
    && apk del $PHPIZE_DEPS



RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && chmod +x /usr/bin/composer

# COPY ./.docker/start.sh /usr/local/bin/start

RUN addgroup -S -g $uid $user \
    && adduser -S -D -u $uid -h /home/$user -G www-data $user

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user
    # && chmod u+x /usr/local/bin/start \
    # && chown -R $user:$user /usr/local/bin/start

# CMD ["/usr/local/bin/start"]

WORKDIR /var/www/html



USER $user

# CMD ["/var/www/html php artisan queue:work"]
# CMD ["/usr/local/bin/start"]
# COPY ./.docker/start.sh /usr/local/bin/start
# RUN chmod +x /usr/local/bin/start
# ENTRYPOINT ["./.docker/start.sh"]
# ENTRYPOINT ["sh", "/usr/local/bin/start"]

# RUN chmod +x start.sh
# ENTRYPOINT ["/start.sh"]

