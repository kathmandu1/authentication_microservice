# FROM php:7.4-fpm-alpine
FROM php:8.1.13-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

COPY crontab /etc/crontabs/root

CMD ["crond", "-f"]
