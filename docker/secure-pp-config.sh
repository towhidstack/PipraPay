#!/bin/bash
# Make pp-config.php readable by the PHP-FPM user only (640).

set +e

# shellcheck source=/app/docker/bootstrap-log.sh
source /app/docker/bootstrap-log.sh
# shellcheck source=/app/docker/detect-php-user.sh
source /app/docker/detect-php-user.sh

resolve_php_group() {
    local user="$1"

    if getent group "$user" >/dev/null 2>&1; then
        echo "$user"
        return 0
    fi

    if getent group nogroup >/dev/null 2>&1; then
        echo "nogroup"
        return 0
    fi

    echo "$user"
}

secure_pp_config_file() {
    local file="$1"
    local php_user group

    [ -f "$file" ] || return 0

    php_user="$(detect_php_user)"

    if ! id "$php_user" >/dev/null 2>&1; then
        php_user="nobody"
    fi

    group="$(resolve_php_group "$php_user")"

    # Nixpacks often blocks `su nobody`; mode 644 is fine inside the app container.
    if [ "$php_user" = "nobody" ] || [ -f /assets/scripts/prestart.mjs ]; then
        chown "${php_user}:${group}" "$file" 2>/dev/null || true
        chmod 644 "$file" 2>/dev/null || true
        piprapay_log "[piprapay] ${file} readable by ${php_user} (644) OK"
        return 0
    fi

    chown "${php_user}:${group}" "$file" 2>/dev/null || true
    chmod 640 "$file" 2>/dev/null || true

    if su -s /bin/sh "$php_user" -c "test -r '$file'"; then
        piprapay_log "[piprapay] ${file} readable by ${php_user} OK"
        return 0
    fi

    chmod 644 "$file" 2>/dev/null || true
    piprapay_log "[piprapay] ${file} readable by ${php_user} (644) OK"
}

if [ "${BASH_SOURCE[0]}" = "${0}" ]; then
    secure_pp_config_file "${1:-/app/pp-config.php}"
fi
