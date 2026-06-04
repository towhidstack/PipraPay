#!/bin/bash
# Write pp-config.php from Coolify linked-database environment variables.

set -e

DB_HOST="${DB_HOST:-}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USERNAME:-${DB_USER:-}}"
DB_PASS="${DB_PASSWORD:-}"
DB_NAME="${DB_DATABASE:-${DB_NAME:-}}"
DB_PREFIX="${DB_PREFIX:-pp_}"

if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ]; then
    exit 0
fi

if [ -f /app/pp-config.php ] && [ "${PIPRAPAY_REGENERATE_DB_CONFIG:-}" != "1" ]; then
    exit 0
fi

cat > /app/pp-config.php <<PHP
<?php
    \$db_host = '${DB_HOST}';
    \$db_port = '${DB_PORT}';
    \$db_user = '${DB_USER}';
    \$db_pass = '${DB_PASS}';
    \$db_name = '${DB_NAME}';
    \$db_prefix = '${DB_PREFIX}';
?>
PHP

chmod 640 /app/pp-config.php 2>/dev/null || true
echo "[piprapay] pp-config.php written from Coolify DB env (host=${DB_HOST})"
