#!/bin/bash
set -e

PORT="${PORT:-8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

mkdir -p /app/pp-media/storage
# Restore DB config from Dokploy volume before optional env auto-write
# shellcheck source=/app/docker/piprapay-config-persist.sh
source /app/docker/piprapay-config-persist.sh
piprapay_restore_config

bash /app/docker/write-pp-config-from-env.sh || true
piprapay_persist_config

chown -R www-data:www-data /app/pp-media /app/pp-media/storage 2>/dev/null || true
chmod -R ug+rwX /app/pp-media/storage 2>/dev/null || true
chmod 775 /app/pp-media/storage 2>/dev/null || true

# Dokploy/host volumes are sometimes mounted as root:root — ensure PHP-FPM can write uploads.
if ! su -s /bin/sh www-data -c "test -w /app/pp-media/storage" 2>/dev/null; then
    echo "[piprapay] WARN: www-data cannot write to /app/pp-media/storage — applying permissive mode" >&2
    chmod 777 /app/pp-media/storage 2>/dev/null || true
fi

su -s /bin/sh www-data -c "touch /app/pp-media/storage/.write-test && rm -f /app/pp-media/storage/.write-test" 2>/dev/null \
    || echo "[piprapay] WARN: upload directory write test failed; logo/QR uploads may fail" >&2

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
    echo "[piprapay] WARN: imagick extension is not loaded — uploads will use direct file copy" >&2
fi

exec "$@"
