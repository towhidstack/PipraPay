#!/bin/bash
# Shared container bootstrap for Dockerfile entrypoint and Nixpacks production-start.

set -e

mkdir -p /app/pp-media/storage

# shellcheck source=/app/docker/piprapay-config-persist.sh
source /app/docker/piprapay-config-persist.sh
piprapay_restore_config

bash /app/docker/write-pp-config-from-env.sh || true
piprapay_persist_config

bash /app/docker/fix-storage-permissions.sh || {
    echo "[piprapay] WARN: upload directory fix failed — logo/QR uploads may fail until volume is fixed" >&2
}

bash /app/docker/secure-pp-config.sh /app/pp-config.php || true

BUILD_VERSION="unknown"
if [ -f /app/BUILD_VERSION ]; then
    BUILD_VERSION="$(tr -d '\n' < /app/BUILD_VERSION)"
fi

IMAGICK_STATUS="$(php -r 'echo extension_loaded("imagick") ? "enabled" : "DISABLED";')"
RUNTIME="unknown"
if [ -f /etc/supervisor/conf.d/supervisord.conf ]; then
    RUNTIME="dockerfile"
elif [ -f /assets/scripts/prestart.mjs ]; then
    RUNTIME="nixpacks"
fi

echo "[piprapay] build=${BUILD_VERSION} php=$(php -r 'echo PHP_VERSION;') imagick=${IMAGICK_STATUS} runtime=${RUNTIME}"

if [ "$IMAGICK_STATUS" != "enabled" ]; then
    echo "[piprapay] WARN: imagick extension is not loaded — uploads will use direct file copy" >&2
fi
