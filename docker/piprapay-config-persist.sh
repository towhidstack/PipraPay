#!/bin/bash
# Persist pp-config.php on the mounted volume (survives app redeploys on Dokploy).

PIPRAPAY_STORED_CONFIG="${PIPRAPAY_STORED_CONFIG:-/app/pp-media/storage/.pp-config.php}"

piprapay_restore_config() {
    if [ -f /app/pp-config.php ]; then
        return 0
    fi
    if [ ! -f "$PIPRAPAY_STORED_CONFIG" ]; then
        return 0
    fi
    cp "$PIPRAPAY_STORED_CONFIG" /app/pp-config.php
    chmod 640 /app/pp-config.php 2>/dev/null || true
    chown www-data:www-data /app/pp-config.php 2>/dev/null || true
    echo "[piprapay] pp-config.php restored from volume (${PIPRAPAY_STORED_CONFIG})"
}

piprapay_persist_config() {
    if [ ! -f /app/pp-config.php ]; then
        return 0
    fi
    mkdir -p "$(dirname "$PIPRAPAY_STORED_CONFIG")"
    cp /app/pp-config.php "$PIPRAPAY_STORED_CONFIG"
    chmod 640 "$PIPRAPAY_STORED_CONFIG" 2>/dev/null || true
    chown www-data:www-data "$PIPRAPAY_STORED_CONFIG" 2>/dev/null || true
}
