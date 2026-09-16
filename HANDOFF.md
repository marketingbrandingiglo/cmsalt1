# HANDOFF — indocyber-cms-renew (CMS backend for IGLO / Indocyber)

Dokumen serah-terima untuk **melanjutkan proyek di sesi/akun Claude Code yang baru**
(mis. setelah dihubungkan ke GitHub).
Terakhir diperbarui: **2026-09-16**.

> Cara pakai di sesi baru: buka Claude Code, hubungkan ke GitHub, buka repo
> `indocyber-cms-renew`, lalu **tunjuk/upload file `HANDOFF.md` ini** dan minta
> Claude "lanjutkan proyek berdasarkan HANDOFF.md".

---

## 1. Ringkasan proyek

CMS custom (**Laravel 13 + Filament 5**, bukan Strapi) untuk mengelola konten
halaman **About Us** situs corporate IGLO / Indocyber. Backend ini berdiri
sendiri — frontend publiknya ada di **repo terpisah** yang sudah live di
Vercel (lihat §4).

- **Repo ini** (`indocyber-cms-renew`) = backend/CMS saja.
- **Belum ada apa pun yang di-deploy.** Semua masih jalan lokal saja, sesuai
  permintaan eksplisit user — jangan push ke remote atau deploy ke mana pun
  tanpa diminta.
- Baca **`CLAUDE.md`** di root repo ini untuk arsitektur lengkap, riwayat
  keputusan (kenapa bukan Strapi), dan aturan keras (jangan expose secret,
  validasi input, wajib minta izin sebelum ubah auth/permission/role).

---

## 2. Struktur

```
indocyber-cms-renew/
├── CLAUDE.md          ← arsitektur, riwayat, aturan keras — WAJIB dibaca
├── README.md          ← cara jalankan lokal + troubleshooting lengkap
├── HANDOFF.md          ← file ini
└── backend/            ← Laravel 13 + Filament 5
    ├── CLAUDE.md        ← guideline Laravel Boost (khusus subfolder ini)
    ├── app/Models/       AboutUs, AboutValue, Milestone, MilestoneEvent,
    │                     Partner, ClientCategory, Client
    ├── app/Filament/     Resources (Milestones, Partners, ClientCategories,
    │                     Clients) + Pages/ManageAboutUs.php (singleton form)
    ├── app/Http/Controllers/Api/AboutUsController.php  ← API read-only
    ├── routes/api.php    GET /api/about-us(+/milestones|/partners|/client-categories)
    ├── database/seeders/AboutUsSeeder.php  ← seed konten bilingual awal
    ├── database/database.sqlite  ← DB lokal (di-gitignore — generate lagi
    │                     lewat `php artisan migrate --seed`, lihat §5)
    └── public/images/iglo-logo.png  ← logo brand (baru ditambahkan)
```

## 3. Status yang sudah selesai

- [x] Content model About Us: **Deskripsi, Visi, Misi, Value, Milestones,
      Partner, Client (+Category)** — bilingual ID/EN via kolom `_id`/`_en`
      langsung (bukan package translasi).
- [x] Panel admin Filament di `/admin`, semua resource CRUD berfungsi.
- [x] Halaman singleton `ManageAboutUs` (Deskripsi/Visi/Misi/Value dalam satu
      form, bukan resource create/delete biasa).
- [x] REST API publik **read-only, tanpa token/secret** (memang sengaja —
      konten marketing publik, lihat CLAUDE.md hard rule #1):
      `GET /api/about-us?locale=id|en`, `/api/about-us/milestones`,
      `/api/about-us/partners`, `/api/about-us/client-categories`.
- [x] Data awal ter-seed (migrasi dari `lib/content.js` frontend lama) via
      `database/seeders/AboutUsSeeder.php` — aman dijalankan ulang.
- [x] Branding: logo IGLO (`public/images/iglo-logo.png`) + warna primer
      merah (`Color::Red`) di `app/Providers/Filament/AdminPanelProvider.php`.
- [x] Frontend (`iglowebsitealt1-claude-web-iglo-continuation-ss2bdm`,
      repo terpisah) sudah disambungkan ke API ini lewat `lib/cms.js`
      (`NEXT_PUBLIC_CMS_URL`) — **sudah dites end-to-end lokal**, ID & EN
      keduanya berfungsi, terverifikasi lewat network log (bukan fallback
      statis).
- [x] Strapi (implementasi awal, sudah digantikan) **sudah dihapus total**.

## 4. Lokasi & environment (PENTING)

| Hal | Nilai |
|---|---|
| **Repo backend ini** | `indocyber-cms-renew`, di-push ke `https://github.com/marketingbrandingiglo/cmsalt1` (repo **public**) |
| **Repo frontend** | `iglowebsitealt1-claude-web-iglo-continuation-ss2bdm` (repo GitHub privat terpisah, `marketingbrandingiglo/iglowebsitealt1`, sudah deploy ke Vercel) — lihat `HANDOFF.md`/`SETUP-CMS.md` di repo itu untuk detailnya |
| **Login admin (dev lokal)** | Tidak ditulis di sini karena repo ini **public** — tanya tim untuk kredensialnya. **Ganti sebelum produksi.** |
| **PHP** | Perlu **≥ 8.3**. Di mesin Windows sebelumnya pakai PHP 8.5 via Chocolatey (`C:\tools\php85`), karena XAMPP di mesin itu cuma PHP 8.2. **Di environment baru (mis. cloud/Linux), ini kemungkinan tidak relevan** — cukup pastikan `php -v` di environment barunya ≥ 8.3 dan sesuaikan. |
| **Database** | SQLite lokal, tidak butuh MySQL. |

Repo ini sudah/akan di-`git init` dan di-push ke
`https://github.com/marketingbrandingiglo/cmsalt1` (public). **Karena
public**, kredensial dev lokal sengaja tidak ditulis di dokumen manapun di
repo ini — tanya tim untuk itu.

## 5. Menjalankan & verifikasi

```bash
cd backend
composer install          # kalau vendor/ belum ada
php artisan migrate        # kalau DB kosong/baru
php artisan db:seed        # isi konten awal (aman diulang, skip kalau sudah ada isi)
php artisan storage:link   # kalau public/storage belum ke-link
php artisan serve --host=127.0.0.1 --port=8000
```
Lalu buka `http://127.0.0.1:8000/admin` (login di atas) dan
`http://127.0.0.1:8000/api/about-us?locale=id` (cek API).

Panduan lengkap + troubleshooting: lihat **README.md** di root repo ini.

## 6. Roadmap / TODO berikutnya

- [ ] Isi **logo Partner & Client** yang sesungguhnya (masih placeholder,
      belum ada data nyata di-upload).
- [ ] Konfirmasi/ubah kredensial admin sebelum ke produksi.
- [ ] Rencanakan hosting untuk backend ini (belum ada — perlu server PHP +
      pertimbangkan pindah dari SQLite ke MySQL/Postgres untuk produksi).
- [ ] Setelah backend live di URL publik, update
      `NEXT_PUBLIC_CMS_URL` di frontend (`.env.local`/env Vercel) supaya
      situs production menarik data dari backend produksi, bukan
      `127.0.0.1`.
- [ ] Pertimbangkan perluas content model ke section lain (Products, News,
      Career, dst.) mengikuti pola yang sama, kalau CMS ini mau dipakai
      lebih dari sekadar About Us.

## 7. Catatan / jebakan yang sudah dipelajari

- **Jangan pernah expose secret ke frontend** — API About Us sengaja publik
  read-only tanpa token, itu memang benar dan aman untuk konten marketing.
  Kalau nanti ada content type yang butuh proteksi tulis, **wajib minta izin
  dulu** sebelum bikin token/ubah permission (lihat CLAUDE.md hard rule #3).
- `iglowebsitealt1-claude-web-iglo-continuation-ss2bdm` (repo frontend) itu
  **referensi + tujuan integrasi**, bukan tempat CMS ini dibangun — sempat
  ada kebingungan soal ini di awal proyek, sudah diluruskan.
- Kalau ganti versi PHP di Windows, urutan PATH penting (`C:\tools\php85`
  vs `C:\xampp\php`) — lihat README.md.
