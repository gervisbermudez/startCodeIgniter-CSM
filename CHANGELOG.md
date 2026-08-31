# Changelog

Product version is `ADMIN_VERSION` in `application/config/constants.php` (also `composer.json` / `package.json`).

## 3.0.0 — 2026-08-31

The CMS that was still documented as a 2016 CodeIgniter starter is now a shippable content platform. This major aligns the public version with what is already on `master`.

Previous labels were out of sync: the panel cache-busted as **1.8.22**, Composer said **2.0.0**, and this file claimed **2.3.0** for a naming pass. Git tags stop at **v1.6.4**. **3.0.0** is the single product number going forward.

### Publish

- **Collections** — Custom Models become collections: schema + items, presets (portfolio, cards, team, FAQ), theme helper `get_collection()`. Admin URLs stay `/admin/custommodels/`.
- **Page embeds** — New/Edit Page inserts published site products as `{{helper(name)}}`. Public expander whitelist: `render_form`, `fragment`, `render_menu`, `render_album`, `render_video`, `render_event`, `get_collection`. Unknown helpers are left as text (no `is_callable`).
- **Events core** — Start/end dates, place, slug. Public `/events` and `/events/{slug}`. Calendar uses `date_start`. Helper `render_event()`.
- **Pages** — Private and scheduled pages respected on the public site. Status updates from the admin. Notes admin removed (never shipped).
- **Site forms** — Inbox, CSV export, submit cooldown, permissions migration. Public `render_form()` in page HTML.

### Operate

- **Analytics** — First-class admin module, KPI dashboard, client tracker, conversion funnel.
- **Notifications inbox** — Per-user rows, bell, “view all”, mark read without delete, emitters from forms/users/backups.
- **Command palette** — Global search in the admin.
- **Settings IA** — Configuration, Data (backups / import / export), Logs. Content stays off the navbar.
- **Admin bar** — Contextual menus on the public site (edit page, cache, form submissions).
- **List UX** — Full-width search navbars, card lists aligned to Pages, optional API pagination.

### Design and build

- Admin **design tokens** (`--st-*`), dark mode on `html.dark-mode`.
- **Vite** compiles SCSS. Gulp is gone. Vue stays unbundled; each page loads its island from Blade `footer_includes`.
- Docker Compose is **web only** (`ci_php56`, port 8081). MySQL lives on the host (`host.docker.internal`). The old `ci_mysql57` / service `db` story is obsolete.

### Schema (apply on existing DBs)

| File | What |
|---|---|
| `application/database/migrations/003_analytics_module.sql` | Analytics |
| `application/database/migrations/005_collections.sql` | Collections |
| `application/database/migrations/006_notifications_inbox.sql` | Notifications |
| `application/database/migrations/007_events_core.sql` | Events |
| `application/database/migrations/008_siteforms_permissions.sql` | Site forms permissions |

Fresh installs: `application/database/start.sql` plus these migrations if not already folded in.

### Docs

README, Docker guide, docs index, and implementation specs marked **shipped**. Product version **3.0.0** in `ADMIN_VERSION`, Composer, and npm.

---

## Historical notes (2.x)

The sections below describe work from 2025 (Docker bootstrap, structure, a partial English rename). They are kept for history. **Do not treat them as current setup:** Compose no longer includes MySQL; table names and permission keys kept historical spelling (`permisions`, `fragmentos`, `albumes`).

---

## Date: 2025-12-16 — v2.3.0: English naming (partial)

Major refactoring toward English controller/model names and PSR-4/CodeIgniter file names. Database tables and permission keys were **not** fully renamed.

### Controllers (Spanish file → English)

- `Archivos.php` → `Files.php`
- `Calendario.php` → `Calendar.php`
- `Categorias.php` → `Categories.php`
- `Configuracion.php` → `Configuration.php`
- `Eventos.php` → `Events.php`
- `Galeria.php` → `Gallery.php`
- `Notas.php` → `Notes.php` (later removed in 3.0)
- `Paginas.php` → `Pages.php`
- `Usuarios.php` → `Users.php`

Admin URLs moved to `/admin/files`, `/admin/pages`, `/admin/users`, etc. Spanish aliases may still exist in `routes.php`.

### Models

- `Fragmentos.php` → `Fragment.php` (table still `fragmentos`)
- `Site_config.php` → `SiteConfig.php`
- Form submit models renamed; `$table` kept on historical names
- `Usergroup_permisions.php` → `UsergroupPermissions.php` (table still `usergroup_permisions`)

### JavaScript

PascalCase component files (`CategoryNewForm.js`, `DataTableComponent.js`, …).

---

## v2.2.0 — Structure

- Root tidy: scripts → `bin/` / `scripts/`, Postman → `docs/api/`, service worker → `public/`
- `uploads/` and `trash/` gitignored with `.gitkeep`
- Cache folder kept in repo structure

---

## v2.1.0 — Security and cache helper

- `db_debug` follows `ENVIRONMENT`
- Input sanitization on several API endpoints
- `cache_helper.php` for site config and fragments
- Composer `minimum-stability` set to `stable` in intent (some packages still track `dev-master` in the lock from that era)

---

## 2025-12-16 — Docker (original)

First containerization: PHP 7.4 Apache image, entrypoint permissions, `.env` `APP_BASE_URL` on port 8081, admin user `gerber` / `admin123`.

**Superseded in 3.0:** Compose no longer starts MySQL. Use host MySQL and `DATABASE_HOSTNAME=host.docker.internal`. Do not follow `docker logs ci_mysql57` from this historical section.
