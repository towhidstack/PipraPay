# PipraPay on Coolify

## Quick setup

1. **Repository:** `github.com/towhidstack/PipraPay` — branch `main`
2. **Build Pack:** **Dockerfile** (faster rebuilds) **or** Nixpacks (first build slower)
3. **Port:** `8080` for Dockerfile, or Coolify-assigned port for Nixpacks
4. **MariaDB 11:** create resource → **Link** to this application
5. **Redeploy** — keep build cache ON for faster 2nd deploy

## Build stuck or very slow?

- First Nixpacks build often takes **5–20 minutes** (downloads PHP + Imagick from Nix).
- Click **Show Debug Logs** — if it shows `nix-env` / `building`, it is working, not frozen.
- Coolify injects `NIXPACKS_NODE_VERSION=22` — `nixpacks.toml` sets `providers = ["php"]` to skip Node.
- For speed: **Configuration → Build Pack → Dockerfile**, then redeploy (uses layer cache).

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
