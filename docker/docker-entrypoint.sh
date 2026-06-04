#!/bin/bash
set -e

PORT="${PORT:-8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

bash /app/docker/write-pp-config-from-env.sh || true

mkdir -p /app/pp-media/storage
chown -R www-data:www-data /app/pp-media/storage

if [ -f /app/pp-config.php ]; then
    chown www-data:www-data /app/pp-config.php
    chmod 640 /app/pp-config.php
fi

BUILD_VERSION="unknown"
if [ -f /app/BUILD_VERSION ]; then
    BUILD_VERSION="$(tr -d '\n' < /app/BUILD_VERSION)"
fi

IMAGICK_STATUS="$(php -r 'echo extension_loaded("imagick") ? "enabled" : "DISABLED";')"
echo "[piprapay] build=${BUILD_VERSION} php=$(php -r 'echo PHP_VERSION;') imagick=${IMAGICK_STATUS} port=${PORT}"

if [ "$IMAGICK_STATUS" != "enabled" ]; then
    echo "[piprapay] ERROR: imagick extension is not loaded" >&2
    exit 1
fi

exec "$@"
