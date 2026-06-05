#!/bin/bash
# Shared container bootstrap for Dockerfile entrypoint and Nixpacks production-start.

set -e

# shellcheck source=/app/docker/bootstrap-log.sh
source /app/docker/bootstrap-log.sh

mkdir -p /app/pp-media/storage

# shellcheck source=/app/docker/piprapay-config-persist.sh
source /app/docker/piprapay-config-persist.sh
piprapay_restore_config

bash /app/docker/write-pp-config-from-env.sh || true
piprapay_persist_config

STORAGE_OK=1
bash /app/docker/fix-storage-permissions.sh || {
    STORAGE_OK=0
    piprapay_warn "[piprapay] WARN: upload directory fix failed — logo/QR uploads may fail until volume is fixed"
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

CONFIG_OK=0
if [ -f /app/pp-config.php ] && [ -r /app/pp-config.php ]; then
    CONFIG_OK=1
fi

if piprapay_verbose; then
    echo "[piprapay] build=${BUILD_VERSION} php=$(php -r 'echo PHP_VERSION;') imagick=${IMAGICK_STATUS} runtime=${RUNTIME}"
else
    echo "[piprapay] ready build=${BUILD_VERSION} storage=$([ "$STORAGE_OK" -eq 1 ] && echo ok || echo fail) config=$([ "$CONFIG_OK" -eq 1 ] && echo ok || echo missing) imagick=${IMAGICK_STATUS} runtime=${RUNTIME}"
fi

if [ "$IMAGICK_STATUS" != "enabled" ]; then
    piprapay_warn "[piprapay] WARN: imagick extension is not loaded — uploads will use direct file copy"
fi
