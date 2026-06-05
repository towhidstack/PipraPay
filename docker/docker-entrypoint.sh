#!/bin/bash
set -e

PORT="${PORT:-8080}"
export PORT

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

bash /app/docker/bootstrap.sh

echo "[piprapay] port=${PORT}"

exec "$@"
