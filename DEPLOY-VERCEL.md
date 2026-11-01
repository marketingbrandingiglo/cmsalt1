# Deploy CMS ke Vercel (gratis) + Neon + Cloudflare R2

CMS (Laravel + Filament) jalan sebagai **container service** di Vercel
(fitur Beta "container runtime"), region Singapore (`sin1`), dekat dengan
database Neon. Tidak perlu kartu kredit.

File terkait (semua di `backend/`):
- `vercel.json` — mendeklarasikan container service + region `sin1`
- `Dockerfile.vercel` — image FrankenPHP (PHP 8.4 + Caddy) yang listen di `$PORT`
- `docker/Caddyfile.vercel`, `docker/vercel-start.sh` — server + start-up
  (`php artisan cms:bootstrap`: migrate, seed hanya saat database masih kosong,
  pastikan akun admin pertama ada)

Data: **Neon** (`DB_URL`). Gambar & upload: **Cloudflare R2** (`MEDIA_DISK=r2`),
termasuk upload sementara Livewire (langsung dari browser ke R2), karena
filesystem container Vercel tidak permanen dan body request dibatasi ±4,5 MB.

## 1. Cloudflare R2 — CORS (sekali saja)

Upload dari admin dikirim browser langsung ke R2, jadi bucket perlu CORS:
R2 → bucket `iglo-media` → **Settings** → **CORS Policy** → **Add CORS policy** →
tab JSON, tempel:

```json
[
  {
    "AllowedOrigins": ["*"],
    "AllowedMethods": ["GET", "PUT", "HEAD"],
    "AllowedHeaders": ["*"],
    "ExposeHeaders": ["ETag"],
    "MaxAgeSeconds": 3600
  }
]
```
(Aman: upload tetap butuh URL bertanda tangan dari server, berlaku 5 menit.)

## 2. Vercel — buat project CMS

1. https://vercel.com/new → **Import Git Repository** → `marketingbrandingiglo/cmsalt1`
   (kalau tidak muncul: **Adjust GitHub App Permissions** → tambahkan `cmsalt1`).
2. **Project Name:** `iglo-cms` · **Framework Preset:** Other ·
   **Root Directory:** `backend`.
3. **Environment Variables** — tempel sekaligus (Vercel menerima format `.env`):
   ```
   APP_KEY=base64:...
   APP_URL=https://iglo-cms.vercel.app
   DB_CONNECTION=pgsql
   DB_URL=postgresql://...
   SESSION_SECURE_COOKIE=true
   MEDIA_DISK=r2
   R2_BUCKET=iglo-media
   R2_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
   R2_PUBLIC_URL=https://pub-....r2.dev
   R2_ACCESS_KEY_ID=...
   R2_SECRET_ACCESS_KEY=...
   ADMIN_EMAIL=design@indocyber.id
   ADMIN_PASSWORD=...
   ```
4. **Deploy.** Deploy pertama ini memakai branch `main` (kode lama) — boleh
   gagal, tidak apa-apa.
5. **Settings → Git → Production Branch** → ubah ke
   `claude/amazing-noether-s6fyuo` → **Save**.
6. **Deployments → Create Deployment** → isi branch
   `claude/amazing-noether-s6fyuo` → **Create**. Build pertama ±5–10 menit.
7. Setelah status **Ready**: cek `https://<domain>/api/home` (JSON) dan
   `https://<domain>/admin` (login dengan `ADMIN_EMAIL`/`ADMIN_PASSWORD`).
   Kalau domainnya bukan `iglo-cms.vercel.app`, ubah `APP_URL` lalu redeploy.

## 3. Frontend

Di project Vercel **frontend**: Settings → Environment Variables →
`NEXT_PUBLIC_CMS_API_URL=https://<domain-cms>/api` → redeploy frontend.

## Update berikutnya

Push ke `claude/amazing-noether-s6fyuo` → Vercel deploy otomatis.

## Catatan

- Container runtime Vercel masih **Beta**; paket Hobby (gratis) ditujukan untuk
  pemakaian non-komersial. Untuk produksi jangka panjang pertimbangkan paket
  berbayar atau hosting yang selalu menyala.
- `render.yaml` / `DEPLOY-RENDER.md` tetap ada sebagai alternatif.
