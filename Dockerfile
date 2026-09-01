# Build multistage e riproducibile.
#
# Volutamente senza direttiva `# syntax=`: non serve nessuna feature del
# frontend Dockerfile esterno, e chiederlo al registry aggiungeva un punto di
# rottura di rete a ogni build.
#
# La versione precedente faceva `composer install` e subito dopo
# `composer require barryvdh/laravel-dompdf laravel/ui` a build time, su
# pacchetti gia' presenti in composer.json: la build dipendeva dalla rete e
# poteva risolvere versioni diverse da quelle del composer.lock.
# Qui si installa esclusivamente da lockfile.
#
# Target disponibili:
#   dev   -> usato da docker-compose e dal devcontainer (codice bind-mounted)
#   prod  -> immagine autosufficiente con vendor e asset gia' dentro

# Laravel 13 (target della migrazione, Project Doc Step 2/M4) richiede PHP
# 8.3 minimo; si parte da 8.4 per restare a distanza di sicurezza dai
# componenti Symfony che alcune patch 13.x richiedono su PHP 8.4 (vedi
# Project Doc, nota su Symfony 8). Verificare la disponibilita' di PHP 8.4
# sull'hosting di destinazione prima del deploy (Step D1).
ARG PHP_VERSION=8.4
ARG NODE_VERSION=20

# ---------------------------------------------------------------------------
# Stage: assets — build Vite
# ---------------------------------------------------------------------------
FROM node:${NODE_VERSION}-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ---------------------------------------------------------------------------
# Stage: base — runtime PHP condiviso
# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-bookworm AS base

ARG UID=1000
ARG GID=1000

# Estensioni: solo quelle realmente usate dall'app.
# gd serve a dompdf per le immagini, pdo_mysql al database, zip a composer.
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions \
        bcmath \
        exif \
        gd \
        intl \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
    && rm -rf /tmp/*

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Utente non-root. UID/GID parametrici per allinearsi all'utente host su Linux
# ed evitare file root-owned nel bind mount.
RUN groupadd -g ${GID} www \
    && useradd -u ${UID} -g ${GID} -ms /bin/bash www

COPY php/local.ini /usr/local/etc/php/conf.d/zz-mexam.ini

WORKDIR /var/www

# ---------------------------------------------------------------------------
# Stage: vendor — dipendenze di produzione, da lockfile
# ---------------------------------------------------------------------------
FROM base AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

# ---------------------------------------------------------------------------
# Stage: dev — bersaglio di compose e devcontainer
# ---------------------------------------------------------------------------
FROM base AS dev

ENV COMPOSER_ALLOW_SUPERUSER=1

# Strumenti richiesti dal VS Code Server quando ci si collega col devcontainer:
# procps per la gestione dei suoi processi, wget come fallback di curl.
# Stanno qui e non in `base` per non appesantire l'immagine di produzione.
USER root
RUN apt-get update \
    && apt-get install -y --no-install-recommends procps wget \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Asset gia' buildati: l'entrypoint li copia nel bind mount se public/build e'
# vuoto, cosi' `docker compose up` da un clone pulito serve subito una pagina
# funzionante senza passare da `npm run build` a mano.
COPY --from=assets /app/public/build /opt/mexam-assets/build

COPY docker/entrypoint.sh /usr/local/bin/mexam-entrypoint
RUN chmod +x /usr/local/bin/mexam-entrypoint

USER www

ENTRYPOINT ["mexam-entrypoint"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
# Stage: prod — immagine autosufficiente
# ---------------------------------------------------------------------------
FROM base AS prod

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --chown=www:www . .
COPY --from=vendor --chown=www:www /var/www/vendor ./vendor
COPY --from=assets --chown=www:www /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && chown -R www:www storage bootstrap/cache

USER www

CMD ["php-fpm"]
