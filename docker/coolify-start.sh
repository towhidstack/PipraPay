#!/bin/bash
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

if [ -x /assets/start.sh ]; then
    exec /assets/start.sh
fi

if [ -f /etc/supervisor/conf.d/supervisord.conf ]; then
    exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
fi

php-fpm -D 2>/dev/null || /usr/local/sbin/php-fpm -D
exec nginx -g 'daemon off;'
