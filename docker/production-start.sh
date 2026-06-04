#!/bin/bash
# PipraPay production start (Dokploy / Docker / Nixpacks).
set -e

bash /app/docker/write-pp-config-from-env.sh || true

BUILD_VERSION="unknown"
if [ -f /app/BUILD_VERSION ]; then
    BUILD_VERSION="$(tr -d '\n' < /app/BUILD_VERSION)"
fi

IMAGICK_STATUS="$(php -r 'echo extension_loaded("imagick") ? "enabled" : "DISABLED";')"
echo "[piprapay] build=${BUILD_VERSION} php=$(php -r 'echo PHP_VERSION;') imagick=${IMAGICK_STATUS}"

if [ "$IMAGICK_STATUS" != "enabled" ]; then
    echo "[piprapay] ERROR: imagick extension is not loaded" >&2
    exit 1
fi

# Nixpacks PHP: prestart.mjs + php-fpm + nginx
if [ -f /assets/scripts/prestart.mjs ] && [ -f /app/nginx.template.conf ]; then
    mkdir -p /var/log/nginx /var/cache/nginx
    node /assets/scripts/prestart.mjs /app/nginx.template.conf /nginx.conf
    php-fpm -y /assets/php-fpm.conf -D
    exec nginx -c /nginx.conf
fi

# Dockerfile image: supervisord
if [ -f /etc/supervisor/conf.d/supervisord.conf ]; then
    exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
fi

PHP_FPM="$(command -v php-fpm 2>/dev/null || true)"
if [ -n "$PHP_FPM" ]; then
    mkdir -p /var/log/nginx /var/cache/nginx
    "$PHP_FPM" -D
    exec nginx -g 'daemon off;'
fi

echo "[piprapay] ERROR: could not start nginx/php-fpm" >&2
exit 1
