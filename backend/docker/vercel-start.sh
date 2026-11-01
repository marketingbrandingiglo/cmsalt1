#!/bin/sh
# Vercel container start: bring the database up to date, seed it only when
# it is brand new (cms:bootstrap), then serve on $PORT.
set -e

php artisan cms:bootstrap

exec frankenphp run --config /etc/frankenphp/Caddyfile
