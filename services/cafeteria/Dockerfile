FROM alpine/git:v2.32.0 as wait
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.9.0/wait /wait

FROM php:7.4.25-fpm-alpine
RUN echo "UTC" > /etc/timezone
COPY --from=wait /wait /wait
RUN chmod +x /wait
RUN set -ex \
    && apk --no-cache add postgresql-dev \
    && docker-php-ext-install pgsql json
WORKDIR /app
COPY ./public .
CMD /wait \
    && php -S 0.0.0.0:80 -t public