#!/bin/bash
# Ensure PipraPay upload dir is writable by PHP-FPM (www-data) on every container start.
# Dokploy/host bind mounts often arrive as root:root 755 — manual chown in Terminal only
# fixes the current shell container until redeploy replaces it or entrypoint fails silently.

set +e

STORAGE_DIR="${PIPRAPAY_STORAGE_PATH:-/app/pp-media/storage}"
MEDIA_DIR="$(dirname "$STORAGE_DIR")"

mkdir -p "$STORAGE_DIR"

if chown -R www-data:www-data "$MEDIA_DIR"; then
    echo "[piprapay] chown www-data on ${MEDIA_DIR} OK"
else
    echo "[piprapay] WARN: chown failed on ${MEDIA_DIR} (normal on some bind mounts / NFS)" >&2
fi

if chmod -R 777 "$STORAGE_DIR"; then
    echo "[piprapay] chmod 777 on ${STORAGE_DIR} OK"
else
    echo "[piprapay] ERROR: chmod failed on ${STORAGE_DIR}" >&2
    ls -lan "$MEDIA_DIR" >&2 || true
    exit 1
fi

if su -s /bin/sh www-data -c "touch '${STORAGE_DIR}/.write-test' && rm -f '${STORAGE_DIR}/.write-test'"; then
    echo "[piprapay] storage writable by www-data OK (${STORAGE_DIR})"
    exit 0
fi

echo "[piprapay] ERROR: www-data cannot write to ${STORAGE_DIR}" >&2
echo "[piprapay] Fix: mount a NAMED volume at /app/pp-media/storage (see DOKPLOY.md §4)" >&2
ls -lan "$MEDIA_DIR" >&2 || true
ls -lan "$STORAGE_DIR" >&2 || true
exit 1
