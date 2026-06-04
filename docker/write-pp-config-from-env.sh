#!/bin/bash
# Write pp-config.php from platform DB env (Dokploy / Docker Compose).

php /app/docker/write-pp-config-cli.php 2>&1 || true
