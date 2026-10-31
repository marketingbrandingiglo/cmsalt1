# Deploy CMS ke Render (gratis) + Neon + Cloudflare R2

Pengganti Railway (masa trial habis). Semua paket gratis:

| Bagian | Layanan | Kenapa |
|---|---|---|
| Aplikasi (Laravel + Filament) | **Render** — web service Docker, plan Free | Auto-deploy tiap ada push ke GitHub |
| Database | **Neon** — PostgreSQL Free | Postgres gratis Render kedaluwarsa ~30 hari; Neon tidak |
| Gambar upload | **Cloudflare R2** — Free (10 GB) | Disk Render Free terhapus tiap deploy/restart |

Konfigurasi Render ada di `render.yaml` (root repo). Upload memakai disk
`MEDIA_DISK` (`public` lokal, `r2` di Render) — lihat `backend/config/filesystems.php`.

> Catatan Render Free: service tidur setelah 15 menit tanpa trafik; request
> pertama sesudahnya butuh ±1 menit. Situs publik tetap tampil (frontend
> memakai teks cadangan), admin terasa lambat di awal.

## 1. Neon (database)

1. Daftar di https://neon.com → **Create project** → region **AWS Asia Pacific (Singapore)**.
2. Di **Connection Details**, salin connection string (format
   `postgresql://user:pass@ep-xxx.ap-southeast-1.aws.neon.tech/neondb?sslmode=require`).
   Ini nilai **`DB_URL`**.

## 2. Cloudflare R2 (gambar)

1. Daftar/login https://dash.cloudflare.com → **R2 Object Storage** → **Create bucket**,
   nama mis. `iglo-media`. Ini **`R2_BUCKET`**.
2. Bucket → **Settings → Public access → R2.dev subdomain → Allow**. Salin URL
   `https://pub-xxxx.r2.dev`. Ini **`R2_PUBLIC_URL`**.
3. R2 → **Manage R2 API Tokens → Create API token**, permission
   **Object Read & Write**, batasi ke bucket `iglo-media`. Salin:
   - Access Key ID → **`R2_ACCESS_KEY_ID`**
   - Secret Access Key → **`R2_SECRET_ACCESS_KEY`**
   - Endpoint `https://<account-id>.r2.cloudflarestorage.com` → **`R2_ENDPOINT`**

## 3. Render (aplikasi)

1. Daftar di https://render.com dengan akun GitHub, beri akses ke repo
   `marketingbrandingiglo/cmsalt1`.
2. **New → Blueprint** → pilih repo `cmsalt1` → Render membaca `render.yaml`
   (service `iglo-cms`, branch `claude/amazing-noether-s6fyuo`, plan Free,
   region Singapore).
3. Render meminta nilai variabel yang kosong:
   | Variabel | Isi |
   |---|---|
   | `APP_KEY` | jalankan `php artisan key:generate --show` di lokal, salin hasilnya (diawali `base64:`) |
   | `APP_URL` | URL service, mis. `https://iglo-cms.onrender.com` (lihat setelah service dibuat; ubah lalu redeploy kalau beda) |
   | `DB_URL` | dari Neon (langkah 1) |
   | `R2_*` | dari R2 (langkah 2) |
4. **Apply**. Deploy pertama menjalankan migrasi + seed otomatis (isi About
   Us, Home, dan logo bawaan ke R2).
5. Akun admin dibuat otomatis saat start dari `ADMIN_EMAIL` + `ADMIN_PASSWORD`
   (Render Free tidak punya Shell). Hanya dibuat sekali; password yang diganti
   nanti tidak ditimpa.
6. Cek: `https://<url-render>/api/home` mengembalikan JSON, dan `/admin` bisa login.

## 4. Frontend (Vercel)

Set `NEXT_PUBLIC_CMS_API_URL=https://<url-render>/api` di project Vercel, lalu redeploy.

## Update berikutnya

Cukup push ke branch `claude/amazing-noether-s6fyuo` — Render deploy otomatis.
