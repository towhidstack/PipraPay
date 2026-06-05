#!/bin/bash
# Ensure PipraPay upload dir is writable by the PHP-FPM user on every container start.
# Dokploy Nixpacks runs PHP as "nobody"; Dockerfile image uses "www-data".

set +e

STORAGE_DIR="${PIPRAPAY_STORAGE_PATH:-/app/pp-media/storage}"
MEDIA_DIR="$(dirname "$STORAGE_DIR")"
MARKER_FILE="${STORAGE_DIR}/.piprapay-perms-ok"

mkdir -p "$STORAGE_DIR"

php_fpm_users() {
    local users="nobody www-data nginx"
    local conf

    for conf in /usr/local/etc/php-fpm.d/zz-www.conf /assets/php-fpm.conf /etc/php*/fpm/pool.d/www.conf; do
        if [ -f "$conf" ]; then
            local parsed
            parsed="$(grep -E '^[[:space:]]*user[[:space:]]*=' "$conf" 2>/dev/null | tail -1 | sed -E 's/.*=[[:space:]]*//; s/[[:space:]]+$//; s/;//')"
            if [ -n "$parsed" ]; then
                users="${parsed} ${users}"
            fi
        fi
    done

    echo "$users" | tr ' ' '\n' | awk 'NF && !seen[$0]++'
}

can_write_as() {
    local user="$1"
    if id "$user" >/dev/null 2>&1; then
        su -s /bin/sh "$user" -c "touch '${STORAGE_DIR}/.write-test' && rm -f '${STORAGE_DIR}/.write-test'"
        return $?
    fi

    return 1
}

echo "[piprapay] storage path: ${STORAGE_DIR}"
if command -v stat >/dev/null 2>&1; then
    stat -c '[piprapay] storage mode=%a owner=%U:%G' "$STORAGE_DIR" 2>/dev/null \
        || stat -f '[piprapay] storage mode=%OLp owner=%Su:%Sg' "$STORAGE_DIR" 2>/dev/null \
        || true
fi

chmod 777 "$MEDIA_DIR" 2>/dev/null || true

if chown -R www-data:www-data "$MEDIA_DIR" 2>/dev/null; then
    echo "[piprapay] chown www-data on ${MEDIA_DIR} OK"
else
    echo "[piprapay] WARN: chown www-data failed on ${MEDIA_DIR} (common on bind mounts / NFS)" >&2
fi

if chmod -R 777 "$STORAGE_DIR"; then
    echo "[piprapay] chmod 777 on ${STORAGE_DIR} OK"
else
    echo "[piprapay] ERROR: chmod failed on ${STORAGE_DIR}" >&2
    ls -lan "$MEDIA_DIR" >&2 || true
    exit 1
fi

WRITE_OK=0
PHP_USER="unknown"

while IFS= read -r user; do
    [ -n "$user" ] || continue
    if can_write_as "$user"; then
        echo "[piprapay] storage writable by ${user} OK (${STORAGE_DIR})"
        WRITE_OK=1
        PHP_USER="$user"
        break
    fi
done < <(php_fpm_users)

if [ "$WRITE_OK" -eq 1 ]; then
    date -u +%Y-%m-%dT%H:%M:%SZ > "$MARKER_FILE" 2>/dev/null || echo ok > "$MARKER_FILE"
    exit 0
fi

echo "[piprapay] ERROR: no PHP user could write to ${STORAGE_DIR}" >&2
echo "[piprapay] Fix: Dokploy → Volumes → mount a NAMED volume at /app/pp-media/storage (see DOKPLOY.md §4)" >&2
echo "[piprapay] Prefer Build type: Dockerfile (not Nixpacks) — Port 8080" >&2
ls -lan "$MEDIA_DIR" >&2 || true
ls -lan "$STORAGE_DIR" >&2 || true
exit 1
