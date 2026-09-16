# indocyber-cms-renew

Custom CMS backend (Laravel + Filament) for the IGLO / Indocyber corporate
site's About Us page. See [CLAUDE.md](./CLAUDE.md) for full architecture,
history (why this isn't Strapi), and hard rules before making changes.

## Structure

- `backend/` — Laravel 13 + Filament 5 admin panel + read-only REST API.

The frontend that consumes this backend's API is a **separate repo**
(`iglowebsitealt1-claude-web-iglo-continuation-ss2bdm`), already deployed
on Vercel — not part of this repo.

## Running locally

See the step-by-step guide below. Short version:

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
# Admin panel: http://127.0.0.1:8000/admin
# API:         http://127.0.0.1:8000/api/about-us
```

Login: local dev admin account — ask a team member for the credentials
(not written here since this repo is public). Change it before any real
deployment.

Nothing here is deployed — everything runs locally by design until told
otherwise.

---

## Full guide: running everything on localhost

This project has two halves that both need to be running at the same time:

1. **Backend (this repo)** — Laravel + Filament, serves the admin panel and
   the API, on `http://127.0.0.1:8000`.
2. **Frontend** — the separate `iglowebsitealt1-claude-web-iglo-continuation-ss2bdm`
   repo (Next.js), on `http://localhost:3000`. This is what actually shows
   the About Us page to visitors, styled — the backend has no visual site
   of its own beyond the admin panel.

You need **two terminal windows/tabs open at the same time**, one per app.

### One-time setup (only needed once per machine)

1. **PHP 8.3 or newer** must be available. This project was set up using
   PHP 8.5 installed via Chocolatey (`choco install php -y`, run from an
   **Administrator** PowerShell), which lands at `C:\tools\php85`. If your
   machine's plain `php -v` shows something older (e.g. XAMPP's PHP 8.2),
   either:
   - Call the right PHP by full path for every command below
     (`C:\tools\php85\php.exe artisan ...`, and prefix `composer` commands
     with `set PATH=C:\tools\php85;%PATH%&&` in cmd, or in PowerShell:
     `$env:Path = "C:\tools\php85;$env:Path"` once per terminal session),
     **or**
   - Reorder your system PATH so `C:\tools\php85` comes before
     `C:\xampp\php` (Windows search → "Edit the system environment
     variables" → Environment Variables → System variables → `Path` →
     Edit → select `C:\tools\php85` → Move Up until it's above
     `C:\xampp\php` → OK everywhere → open a **new** terminal to pick it up).
2. **Composer dependencies** (only needed once, or after a `composer.json`
   change):
   ```bash
   cd backend
   composer install
   ```
3. **Node dependencies for the frontend** (only needed once, or after a
   `package.json` change), in the frontend repo:
   ```bash
   cd "../iglowebsitealt1-claude-web-iglo-continuation-ss2bdm"
   npm install
   ```

### Every time you want to run it

**Terminal 1 — backend:**
```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```
Leave this running. You should see `Server running on [http://127.0.0.1:8000]`.

- Admin panel: http://127.0.0.1:8000/admin
  (login: ask a team member for the local dev admin credentials)
- API (what the frontend calls): http://127.0.0.1:8000/api/about-us

**Terminal 2 — frontend:**
```bash
cd "../iglowebsitealt1-claude-web-iglo-continuation-ss2bdm"
npm run dev
```
Leave this running too. You should see `Local: http://localhost:3000`.

Then open **http://localhost:3000/about** in your browser — that's the
real page, pulling live content from the backend you started in Terminal 1.

The frontend already knows where to find the backend via
`iglowebsitealt1-claude-web-iglo-continuation-ss2bdm/.env.local`
(`NEXT_PUBLIC_CMS_URL=http://127.0.0.1:8000`) — no extra config needed as
long as the backend is running on port 8000.

### Editing content

1. Open http://127.0.0.1:8000/admin, log in.
2. Edit **About Us** (Deskripsi/Visi/Misi/Value), or the **Milestones**,
   **Partners**, **Client Categories**, **Clients** lists in the sidebar.
   Click **Save**.
3. Refresh http://localhost:3000/about — the change appears (may take up
   to ~60 seconds due to the frontend's fetch cache, or refresh twice).

### Stopping everything

`Ctrl+C` in each terminal window. Closing the terminal windows also stops
the servers.

### Troubleshooting

- **`php artisan serve` fails or `php -v` shows the wrong version** — see
  the PHP PATH note in "One-time setup" above.
- **Frontend page shows placeholder/static content instead of your edits**
  — the backend server (Terminal 1) probably isn't running, or crashed.
  Check that terminal for errors and that
  http://127.0.0.1:8000/api/about-us returns JSON in your browser directly.
- **`Class "..." not found` errors from Laravel/Filament** — run
  `composer install` again in `backend/`.
