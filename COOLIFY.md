# PipraPay on Coolify

## Quick setup

1. **Repository:** `github.com/towhidstack/PipraPay` — branch `main`
2. **Build Pack:** Nixpacks (default) **or** Dockerfile
3. **Port:** match Coolify (often `4000` for Nixpacks, `8080` for Dockerfile)
4. **MariaDB 11:** create resource → **Link** to this application
5. **Redeploy** with build cache cleared

## Verify after deploy

- `https://YOUR-DOMAIN/pp-health.php` → `"imagick": "enabled"`
- Runtime logs → `[piprapay] imagick=enabled`

## Database

Do **not** use `localhost` as DB host. Use the **internal hostname** from the linked MariaDB service, or let Coolify inject `DB_HOST` / `DB_DATABASE` (auto-writes `pp-config.php` on start).

To re-run the web installer:

```bash
rm -f /app/pp-config.php
```

## Imagick

Installed at build time via `nixpacks.toml` (Nixpacks) or `Dockerfile` (Docker build).
