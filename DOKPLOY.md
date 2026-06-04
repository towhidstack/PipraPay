# PipraPay on Dokploy

Repository: `https://github.com/towhidstack/PipraPay` — branch `main`

## 1. Application settings

| Setting | Value |
|---------|--------|
| Build type | **Dockerfile** (recommended — Imagick built in) |
| Dockerfile path | `Dockerfile` |
| Docker context | `.` |
| Port | **8080** |

Alternative: **Nixpacks** + `nixpacks.toml` (first build 5–15 min).

---

## 2. MariaDB

1. Same Dokploy **Project** as TaqwaMart → Create **MariaDB 11**  
2. Name service e.g. `piprapay-mariadb`  
3. Database: `piprapaydb`, user/password as you prefer  

**Never use `localhost` in the installer** — use the MariaDB **service name** as host.

### MariaDB auth (same as Coolify)

In Coolify **Custom Docker Options** you may have used:

```text
--default-authentication-plugin=mysql_native_password
```

On Dokploy: MariaDB resource → **Advanced** → add that argument → restart DB.  
If the DB was created earlier without it, reset the database or `ALTER USER ... IDENTIFIED VIA mysql_native_password` (see `app/DOKPLOY.md`).

---

## 3. Environment variables

See `.env.dokploy.example`.

```env
PORT=8080
PIPRAPAY_APP_URL=https://pay.taqwamart.bd
```

`PIPRAPAY_APP_URL` must use **`https://`**. Without it:

- CSS/JS load as `http://...` (Mixed Content, blocked by the browser)
- Admin menu AJAX posts to `http://...` while you are on HTTPS → cookies are not sent → Gateways (and other pages) show `{"status":"false","message":"invalid request"}` and ApexCharts may log `NaN` on the dashboard

### Web installer (default)

1. Deploy without `pp-config.php`  
2. Open `https://pay.taqwamart.bd/`  
3. Step 2: DB host = `piprapay-mariadb` (your service name)  
4. If tables already exist: tick **Remove existing PipraPay tables and re-import**  
5. Step 3: Admin account  

### Skip installer (optional)

After DB exists and schema imported once:

```env
PIPRAPAY_AUTO_DB_CONFIG=1
DB_HOST=piprapay-mariadb
DB_PORT=3306
DB_DATABASE=piprapaydb
DB_USERNAME=piprapay
DB_PASSWORD=your-password
DB_PREFIX=pp_
```

Redeploy — `pp-config.php` is written on start.

---

## 4. Volumes

| Mount | Purpose |
|-------|---------|
| `/app/pp-media/storage` | uploads, QR, media |

---

## 5. Health check

| Path | `/pp-health.php` |
| Port | `8080` |

Expect: `"imagick": "enabled"`, `"database": "connected"` (after install).

---

## 6. Reset / reinstall

PipraPay terminal:

```bash
rm -f /app/pp-config.php /app/pp-temp-config.php
```

MariaDB terminal:

```bash
mysql -u root -p"$MARIADB_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS piprapaydb; CREATE DATABASE piprapaydb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

Then run the web installer again.

---

## 7. TaqwaMart connection

In TaqwaMart (`.env` or admin):

```env
PIPRAPAY_BASE_URL=https://pay.taqwamart.bd
PIPRAPAY_SANDBOX=false
PIPRAPAY_API_KEY=<API key from PipraPay admin>
```

Webhook/return URLs are generated from TaqwaMart `APP_URL` automatically.

---

## 8. Verify deploy

Runtime logs:

```text
[piprapay] build=... imagick=enabled
```

Browser: `https://pay.taqwamart.bd/pp-health.php`
