#!/bin/sh
# Container entrypoint: prepare Laravel, then serve on Railway's $PORT.
set -e

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs

php artisan storage:link --force
php artisan migrate --force

# Seed initial content only when asked (set SEED_ON_DEPLOY=true for the
# first deploy, then remove it) — otherwise content an editor deliberately
# deleted would come back on every redeploy.
if [ "$SEED_ON_DEPLOY" = "true" ]; then
    php artisan db:seed --force
fi

php artisan optimize
php artisan filament:optimize

export SERVER_NAME=":${PORT:-8080}"
exec frankenphp run --config /etc/frankenphp/Caddyfile
