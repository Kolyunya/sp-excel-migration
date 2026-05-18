FROM php:8.5-cli-alpine AS development
WORKDIR /app
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apk add --no-cache git
RUN echo "memory_limit = -1" > /usr/local/etc/php/conf.d/memory-limit.ini

FROM development AS production
WORKDIR /app
COPY . /app
RUN composer install --no-dev --optimize-autoloader
ENTRYPOINT ["/usr/local/bin/php", "app.php", "app:create-table"]
