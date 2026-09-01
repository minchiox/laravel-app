#!/usr/bin/env bash
#
# Porta il container a uno stato usabile prima di avviare php-fpm, cosi' che
# `docker compose up` da un clone pulito basti da solo.
#
# Ogni passo e' idempotente e condizionato: su un progetto gia' inizializzato
# l'entrypoint non fa nulla e avvia direttamente php-fpm.
# Disattivabile con MEXAM_AUTO_SETUP=false.

set -euo pipefail

cd /var/www

log() { printf '\033[0;36m[mexam]\033[0m %s\n' "$*"; }

if [ "${MEXAM_AUTO_SETUP:-true}" = "true" ]; then

    if [ ! -f .env ]; then
        log ".env assente, lo creo da .env.example"
        cp .env.example .env
    fi

    if [ ! -f vendor/autoload.php ]; then
        log "vendor/ assente, eseguo composer install (puo' richiedere qualche minuto)"
        composer install --no-interaction --prefer-dist
    fi

    if ! grep -qE '^APP_KEY=base64:' .env; then
        log "APP_KEY non impostata, la genero"
        php artisan key:generate --force
    fi

    # Se non e' mai stato lanciato `npm run build` sull'host, riuso gli asset
    # compilati dentro l'immagine dallo stage `assets`.
    if [ ! -f public/build/manifest.json ] && [ -d /opt/mexam-assets/build ]; then
        log "asset Vite assenti, copio quelli compilati nell'immagine"
        mkdir -p public/build
        cp -R /opt/mexam-assets/build/. public/build/
    fi

    mkdir -p public/avatars public/pdf storage/framework/{cache/data,sessions,views} storage/logs
    chmod -R ug+rwX storage bootstrap/cache public/avatars public/pdf 2>/dev/null || true

    log "attendo il database"
    for _ in $(seq 1 30); do
        if php -r 'exit(@fsockopen(getenv("DB_HOST") ?: "db", (int)(getenv("DB_PORT") ?: 3306)) ? 0 : 1);' 2>/dev/null; then
            break
        fi
        sleep 2
    done

    log "eseguo le migrazioni"
    php artisan migrate --force

    # Il seed va fatto una volta sola: il seeder dei quiz non e' idempotente e
    # a ogni riavvio ne aggiungerebbe altri 50. Per rifarlo da zero: make fresh
    if [ ! -f storage/.mexam-seeded ]; then
        log "primo avvio, popolo il database con i dati di demo"
        php artisan db:seed --force
        touch storage/.mexam-seeded
    fi

    log "pronto su http://localhost:${APP_PORT:-80}"
fi

exec "$@"
