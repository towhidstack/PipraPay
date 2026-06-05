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

## 4. Volumes (required for config + uploads)

| Mount | Purpose |
|-------|---------|
| `/app/pp-media/storage` | uploads, QR, media, **and** `.pp-config.php` backup |

### Why the installer appears after every redeploy

PipraPay stores DB credentials in `/app/pp-config.php` inside the **app container**. Redeploy replaces that container — the file is gone. **MariaDB data is not deleted**; only the small config file was lost.

Fix (pick one or both):

1. **Volume** — mount `/app/pp-media/storage` (above). On install complete, config is copied to `pp-media/storage/.pp-config.php` and restored on next container start.
2. **Environment** — set `PIPRAPAY_AUTO_DB_CONFIG=1` and all `DB_*` vars so config is recreated on every start (see §3).

Without volume or env, you will see Step 1 (Requirements) again even though the database still has your admin and gateways.

### Upload permissions reset after every redeploy (logo / QR / favicon fail)

**Symptom:** `Cannot write to /app/pp-media/storage/ (PHP runs as nobody)` — uploads fail; manual `chmod` in Terminal works briefly, then breaks again.

**Why it keeps coming back**

| Cause | What happens |
|-------|----------------|
| **Nixpacks build (most common)** | Dokploy auto-detects Nixpacks → PHP runs as **`nobody`**, not `www-data`. Logs show `Server starting on port 80` (not `port=8080`). Old Nixpacks start script did **not** run permission fix on boot. |
| **Redeploy = new container** | Terminal `chmod` fixes only the **current** container. Next redeploy/restart replaces it; fix must run in **bootstrap on start** (now automatic in both Dockerfile and Nixpacks). |
| **No volume mount** | `/app/pp-media/storage` lives inside the disposable container layer. Redeploy wipes permissions. **You must mount a volume.** |
| **Bind mount `root:root` 755** | Host paths often ignore `chown` from inside the container. Use a Dokploy **named volume**, not a random host folder. |
| **Multiple replicas** | Terminal fixes one pod; HTTP may hit another. Scale to **1** while testing. |

**Correct Dokploy setup (do all three)**

1. **Build type → Dockerfile** (not Nixpacks). Port **8080**. Dockerfile path: `Dockerfile`.
2. Application → **Volumes** → mount **`/app/pp-media/storage`** as a **named volume** (e.g. `piprapay-media`).
3. **Redeploy** (push latest `main` first). Do **not** rely on manual Terminal `chmod` after each deploy.

**After redeploy, logs must show:**

```text
[piprapay] chmod 777 on /app/pp-media/storage OK
[piprapay] storage writable by www-data OK
```

or (Nixpacks):

```text
[piprapay] storage writable by nobody OK
```

**Verify in browser:** `https://pay.taqwamart.bd/pp-health.php`

```json
"storage_writable_probe": true,
"bootstrap_permissions_ok": true,
"pp_config_readable": true,
"php_user": "www-data"
```

(`php_user: "nobody"` means you are still on Nixpacks — switch to Dockerfile.)

### HTTP 500 on every page (cron, admin, checkout)

If storage logs show **writable by nobody OK** but the site returns **500** with a tiny response body, `pp-config.php` is often owned by **`www-data`** with mode **640** while Nixpacks PHP runs as **`nobody`** — PHP cannot read DB credentials.

Deploy logs should include:

```text
[piprapay] /app/pp-config.php readable by nobody OK
```

**Immediate fix (Terminal, until redeploy):**

```bash
chown nobody:nogroup /app/pp-config.php
chmod 640 /app/pp-config.php
```

Then reload the site. Push latest `main` and **Redeploy** so `secure-pp-config.sh` applies this automatically on every start.

**If logs still show ERROR**

- Switch from host bind mount to a **named volume**.
- Ensure only **one** volume at `/app/pp-media/storage` (not the whole `/app`).
- Scale replicas to **1**, redeploy, test upload, then scale back.

**Manual chmod is for one-time debug only** (entrypoint/bootstrap handles every start):

```bash
chmod -R 777 /app/pp-media/storage
```

If Terminal upload works but fails after **redeploy without changing anything**, you are missing the **volume mount** or using the wrong **build type**.

### Tables exist but installer Step 2 appears

That is normal when **`pp-config.php` is missing** in the container. PipraPay does not check “tables exist” to skip install — it only checks for `pp-config.php`.

- **Do not** click Check & Import again unless you intend to wipe data (leave “Remove existing tables” **unchecked**).
- Set env below, redeploy, then open **`/login`** (admin already in DB).
- If you see **500**: open `/pp-health.php` — often wrong `DB_PASSWORD`, wrong `DB_HOST`, or MariaDB auth plugin (see `app/DOKPLOY.md` § MariaDB authentication).

### Env checklist (must all be set, then **Redeploy**)

```env
PIPRAPAY_APP_URL=https://pay.taqwamart.bd
PIPRAPAY_AUTO_DB_CONFIG=1
DB_HOST=<exact Internal Host from MariaDB Connection tab>
DB_PORT=3306
DB_DATABASE=piprapaydb
DB_USERNAME=mariadb
DB_PASSWORD=<real password, no quotes>
DB_PREFIX=pp_
PORT=8080
```

Deploy logs should show: `[piprapay] pp-config.php ready (env or volume)`

Terminal check:

```bash
ls -la /app/pp-config.php
cat /app/pp-config.php
```

---

## 5. Whitelist TaqwaMart domains (required for Laravel checkout)

`POST /api/checkout/redirect` validates **return** and **webhook** URLs against the **Domains** table.

In PipraPay admin → **Domains**, add each host your store uses (from TaqwaMart `APP_URL`), for example:

| Domain | Status |
|--------|--------|
| `taqwamart.bd` | Active |
| `www.taqwamart.bd` | Active (if you use www) |

Without this, Laravel (`GuzzleHttp`) reaches PipraPay but gets **HTTP 400** (~105 bytes = often `INVALID_API_KEY`; longer body = `INVALID_URL` domain not whitelisted).

Copy the API key with the **copy button** on the API list (full 50 characters), not the truncated visible text in the input.

---

## 6. Health check

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
