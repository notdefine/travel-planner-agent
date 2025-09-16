FROM php:8.3-cli
RUN apt-get update && apt-get install unzip git -y
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
WORKDIR /app