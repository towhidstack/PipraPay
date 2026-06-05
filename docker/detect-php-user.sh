#!/bin/bash
# Print the PHP-FPM pool user (www-data on Dockerfile, nobody on Nixpacks).

detect_php_user() {
    local conf parsed

    for conf in /assets/php-fpm.conf /usr/local/etc/php-fpm.d/zz-www.conf /etc/php*/fpm/pool.d/www.conf; do
        if [ -f "$conf" ]; then
            parsed="$(grep -E '^[[:space:]]*user[[:space:]]*=' "$conf" 2>/dev/null | tail -1 | sed -E 's/.*=[[:space:]]*//; s/[[:space:]]+$//; s/;//')"
            if [ -n "$parsed" ]; then
                echo "$parsed"
                return 0
            fi
        fi
    done

    if id www-data >/dev/null 2>&1 && [ -f /etc/supervisor/conf.d/supervisord.conf ]; then
        echo "www-data"
        return 0
    fi

    if id nobody >/dev/null 2>&1; then
        echo "nobody"
        return 0
    fi

    echo "www-data"
}

if [ "${BASH_SOURCE[0]}" = "${0}" ]; then
    detect_php_user
fi
