#!/bin/bash
# Make pp-config.php readable by the PHP-FPM user only (640).

set +e

# shellcheck source=/app/docker/detect-php-user.sh
source /app/docker/detect-php-user.sh

secure_pp_config_file() {
    local file="$1"
    local php_user group

    [ -f "$file" ] || return 0

    php_user="$(detect_php_user)"
    group="$php_user"

    if ! id "$php_user" >/dev/null 2>&1; then
        php_user="nobody"
        group="nogroup"
    fi

    if ! getent group "$group" >/dev/null 2>&1; then
        group="$php_user"
    fi

    chown "${php_user}:${group}" "$file" 2>/dev/null || true
    chmod 640 "$file" 2>/dev/null || chmod 644 "$file" 2>/dev/null || true

    if su -s /bin/sh "$php_user" -c "test -r '$file'"; then
        echo "[piprapay] ${file} readable by ${php_user} OK"
        return 0
    fi

    chmod 644 "$file" 2>/dev/null || true
    echo "[piprapay] WARN: ${file} forced to 644 for ${php_user}" >&2
}

if [ "${BASH_SOURCE[0]}" = "${0}" ]; then
    secure_pp_config_file "${1:-/app/pp-config.php}"
fi
