#!/bin/bash
# Ensure PipraPay upload dir is writable by the PHP-FPM user on every container start.
# Dokploy Nixpacks runs PHP as "nobody"; Dockerfile image uses "www-data".

set +e

# shellcheck source=/app/docker/bootstrap-log.sh
source /app/docker/bootstrap-log.sh
# shellcheck source=/app/docker/detect-php-user.sh
source /app/docker/detect-php-user.sh

STORAGE_DIR="${PIPRAPAY_STORAGE_PATH:-/app/pp-media/storage}"
MEDIA_DIR="$(dirname "$STORAGE_DIR")"
MARKER_FILE="${STORAGE_DIR}/.piprapay-perms-ok"
PHP_USER="$(detect_php_user)"
PHP_GROUP="$PHP_USER"

if ! id "$PHP_USER" >/dev/null 2>&1; then
    PHP_USER="nobody"
    PHP_GROUP="nogroup"
fi

if ! getent group "$PHP_GROUP" >/dev/null 2>&1; then
    PHP_GROUP="$PHP_USER"
fi

mkdir -p "$STORAGE_DIR"

php_fpm_users() {
    local users="${PHP_USER} nobody www-data nginx"
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

piprapay_log "[piprapay] storage path: ${STORAGE_DIR}"
if piprapay_verbose && command -v stat >/dev/null 2>&1; then
    stat -c '[piprapay] storage mode=%a owner=%U:%G' "$STORAGE_DIR" 2>/dev/null \
        || stat -f '[piprapay] storage mode=%OLp owner=%Su:%Sg' "$STORAGE_DIR" 2>/dev/null \
        || true
fi

chmod 755 "$MEDIA_DIR" 2>/dev/null || true
chown "${PHP_USER}:${PHP_GROUP}" "$STORAGE_DIR" 2>/dev/null || true

if chmod 777 "$STORAGE_DIR"; then
    piprapay_log "[piprapay] chmod 777 on ${STORAGE_DIR} OK"
else
    piprapay_warn "[piprapay] ERROR: chmod failed on ${STORAGE_DIR}"
    ls -lan "$MEDIA_DIR" >&2 || true
    exit 1
fi

find "$STORAGE_DIR" -type d -exec chmod 777 {} \; 2>/dev/null || true
find "$STORAGE_DIR" -type f -exec chmod 644 {} \; 2>/dev/null || true

WRITE_OK=0

while IFS= read -r user; do
    [ -n "$user" ] || continue
    if can_write_as "$user"; then
        piprapay_log "[piprapay] storage writable by ${user} OK (${STORAGE_DIR})"
        WRITE_OK=1
        break
    fi
done < <(php_fpm_users)

if [ "$WRITE_OK" -eq 1 ]; then
    date -u +%Y-%m-%dT%H:%M:%SZ > "$MARKER_FILE" 2>/dev/null || echo ok > "$MARKER_FILE"
    exit 0
fi

piprapay_warn "[piprapay] ERROR: no PHP user could write to ${STORAGE_DIR}"
piprapay_warn "[piprapay] Fix: Dokploy → Volumes → mount a NAMED volume at /app/pp-media/storage (see DOKPLOY.md §4)"
piprapay_warn "[piprapay] Prefer Build type: Dockerfile (not Nixpacks) — Port 8080"
ls -lan "$MEDIA_DIR" >&2 || true
ls -lan "$STORAGE_DIR" >&2 || true
exit 1
