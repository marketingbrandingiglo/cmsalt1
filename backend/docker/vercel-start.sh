#!/bin/sh
# Vercel container start. The web server must listen on $PORT right away (a
# slow start counts as a failed invocation), so migrations and the initial
# seed (cms:bootstrap) run in the background; output goes to the runtime logs.
php artisan cms:bootstrap &

exec frankenphp run --config /etc/frankenphp/Caddyfile
