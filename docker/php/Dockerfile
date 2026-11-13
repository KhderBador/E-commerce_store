# syntax=docker/dockerfile:1

FROM ubuntu:24.04

ARG PHP_VERSION=8.5

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

WORKDIR /var/www/html

# ---------------------------------------------------------
# Base system dependencies
# ---------------------------------------------------------

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update && \
    apt-get install -y \
        ca-certificates \
        curl \
        git \
        unzip \
        zip \
        gnupg \
        software-properties-common \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libonig-dev \
        libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# PHP repository
# ---------------------------------------------------------

RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://packages.sury.org/php/apt.gpg \
        | gpg --dearmor -o /etc/apt/keyrings/sury-php.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/sury-php.gpg] https://packages.sury.org/php/ noble main" \
        > /etc/apt/sources.list.d/sury-php.list

# ---------------------------------------------------------
# PHP
# ---------------------------------------------------------

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update && \
    apt-get install -y \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-pgsql \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-zip \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-readline \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-redis \
    && rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# Composer
# ---------------------------------------------------------

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------------------------------------------------------
# Node.js
# ---------------------------------------------------------

ARG NODE_VERSION=24

RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_VERSION}.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update && \
    apt-get install -y nodejs && \
    npm install -g npm && \
    rm -rf /var/lib/apt/lists/*

# ---------------------------------------------------------
# Application
# ---------------------------------------------------------

COPY composer.json composer.lock ./

RUN --mount=type=cache,target=/root/.composer/cache \
    composer install \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
        --no-autoloader

COPY package*.json ./

RUN --mount=type=cache,target=/root/.npm \
    npm install

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]