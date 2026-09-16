# CLAUDE.md

Guidance for Claude Code (and any contributor) working in this repository.

## Project

Renew of Indocyber's corporate site: a headless CMS setup.

- **`backend/`** — Laravel 13 + Filament 5 admin panel. Owns all content,
  media, and the editor UI. Content editors work only in the Filament admin
  panel (`/admin`).
- The actual public frontend is a **separate, existing repo**:
  `iglowebsitealt1-claude-web-iglo-continuation-ss2bdm` (Next.js, already
  deployed on Vercel). This repo does not contain its own frontend — the
  Vercel-hosted site is the consumer of this backend's API.

## Architecture

- **Stack: Laravel + Filament (backend/CMS), not Strapi.** This was a
  deliberate switch made mid-project — see "History" below. The original
  Strapi implementation has been deleted.
- Laravel/Filament and the Next.js frontend are **two separate
  applications** that communicate **only over HTTP**, via a small custom
  read-only REST API (`routes/api.php` → `Api\AboutUsController`,
  `GET /api/about-us`, `/api/about-us/milestones`, `/api/about-us/partners`,
  `/api/about-us/client-categories`).
- The frontend never accesses the backend's database, filesystem, or source
  code directly, and vice versa. The only integration point is the HTTP API.
- Content flow: an editor changes content in the Filament admin → the
  frontend fetches the updated content via the API → the change appears on
  the public site.
- Bilingual (ID/EN) content uses plain `_id`/`_en` suffixed columns per
  field (e.g. `description_id`, `description_en`), not a translation
  package — the API resolves the right column server-side from a `locale`
  query param and returns already-localized JSON.
- Local dev: SQLite (`database/database.sqlite`), no MySQL required. Media
  uploads go through Filament's `FileUpload` to the `public` disk
  (`php artisan storage:link` already run).
- PHP for this project is 8.5 (via Chocolatey, installed to `C:\tools\php85`)
  — not necessarily first on the machine's system PATH (XAMPP's PHP 8.2 may
  still resolve first for a plain `php` command). See README.md for how to
  run commands with the right PHP.
- **Nothing here is deployed yet.** Everything currently runs locally only,
  by explicit request — do not push to git remotes or deploy anywhere
  (Vercel env vars, a hosting provider, etc.) unless the user asks.

## History

- Originally scaffolded with Strapi. The user asked for a custom-built CMS
  instead of Strapi, so the Strapi backend was replaced and later deleted
  entirely at the user's request (no need to look for it — it's gone).
- The real intended frontend turned out to be an existing, separately
  deployed repo (`iglowebsitealt1-claude-web-iglo-continuation-ss2bdm`,
  brand "IGLO"), which already had an empty Laravel + Filament scaffold in
  its own `backend/` folder (see that repo's `SETUP-CMS.md`) as the
  long-planned real backend. Per explicit user instruction, that stack
  (Laravel + Filament) is built out **here**, in `indocyber-cms-renew`, not
  inside the other repo — the other repo is reference-only (its frontend
  code for content/page structure, its `backend/SETUP-CMS.md` for the
  intended stack and content-model plan).
- Content model (About Us: Deskripsi, Visi, Misi, Value, Milestones,
  Partner, Client) mirrors what was first built in Strapi, migrated 1:1 to
  Laravel Eloquent models + Filament resources.

## Hard rules (non-negotiable)

1. **Never expose secrets or API keys to the frontend.**
   Any admin credential, database credential, or third-party secret must
   stay server-side only:
   - Never referenced inside a Next.js `'use client'` file.
   - Never assigned to a `NEXT_PUBLIC_*` environment variable.

   (A real past mistake to not repeat: the old Next.js site put a reCAPTCHA
   *secret* key in a `NEXT_PUBLIC_` env var, shipping it to every visitor's
   browser.)

   The current About Us API is intentionally public/read-only with **no
   secret at all** — that's fine and simpler than a token, as long as it
   stays read-only (see rule 3).

2. **All public endpoints must validate input.**
   Every Laravel route/controller and every Next.js Route Handler that
   accepts external input must validate type, shape, and required fields,
   and sanitize the input before using it. Never trust client-supplied data
   as-is. (The current About Us API is read-only with no user input beyond
   a `locale` query param, which is validated/defaulted in the controller.)

3. **Never change anything related to auth, permissions, or roles without
   asking first — always stop and get explicit confirmation before
   touching:**
   - Filament panel authentication/authorization (guards, policies, which
     users can access `/admin`)
   - Any change that would let the public API write data, not just read it
   - Laravel's `auth` config, middleware, or session logic
   - Next.js auth/session logic

   This applies even if the change looks small or seems implied by an
   unrelated task — ask first, every time.

## Working agreement

- Big steps (scaffolding a new app, defining content types/migrations,
  changing the API contract between frontend/backend) need explicit
  approval before starting — don't jump ahead to later stages unprompted.
- `backend/CLAUDE.md` (generated by Laravel Boost) has detailed
  Laravel/Filament/PHP conventions for that subfolder — follow it for
  anything inside `backend/`. This file's hard rules above still apply on
  top of it.
- Don't push to git remotes or deploy (Vercel, a hosting provider, etc.)
  without being asked — everything currently runs locally only.
