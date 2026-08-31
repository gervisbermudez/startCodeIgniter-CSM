![Start CMS](https://repository-images.githubusercontent.com/233129678/7ad83200-f12e-11ea-8538-ab49ede15585)

# Start CMS

**Version 3.0.0** — a self-hosted content platform for sites that need a real admin, a themable public site, and a REST API, without WordPress.

Editors publish pages, collections, events, and forms. Developers own a Blade theme and `/api/v1`. The stack is PHP 7.4, CodeIgniter 3.1, Vue 2, and Materialize — small enough to run on one Docker container and a MySQL you already have.

Repo: [startCodeIgniter-CSM](https://github.com/gervisbermudez/startCodeIgniter-CSM). Brand in the panel: **Start CMS**.

## Why 3.0

3.0 is the product that is actually on `master`, not the 2016 “lightweight CI CMS” listing.

| Editors get | The public site gets | Operators get |
|---|---|---|
| Pages with drafts, schedule, and private | Blade themes | Users, groups, permissions |
| Collections (portfolio, team, FAQ, cards) | `{!! get_collection('slug') !!}` | Analytics dashboard |
| Insert forms, albums, videos, events into a page | Token expander `{{render_form(name)}}` | Notifications inbox |
| Events with dates, slug, `/events` | Event listing and detail | Command palette (search) |
| Site forms + submissions inbox | `render_form()` in the theme | Automatic DB backups |
| Fragments, menus, albums, videos | Contextual admin bar when logged in | Import / export, dark mode |

Not a page builder. Not Gutenberg. Content stays HTML (Trumbowyg) plus named helpers the theme already understands.

## Quick start

**Needs:** Docker, a MySQL 5.7+ instance (`start_cms_db`), Node 18+ if you change SCSS/JS.

Compose **only** starts Apache/PHP (`ci_php56` on port **8081**). It does not start MySQL.

```bash
git clone https://github.com/gervisbermudez/startCodeIgniter-CSM.git
cd startCodeIgniter-CSM
cp .env.example .env   # DATABASE_HOSTNAME=host.docker.internal
docker compose up -d
npm install && npm run build
```

| | |
|---|---|
| Public site | http://localhost:8081 |
| Admin | http://localhost:8081/admin/login |
| User / password | `gerber` / `admin123` |

Change that password before any shared environment. Details: [docs/DOCKER.md](docs/DOCKER.md).

### Default `.env`

```
APP_ENV=development
APP_BASE_URL=http://localhost:8081/
DATABASE_HOSTNAME=host.docker.internal
DATABASE_DATABASE=start_cms_db
DATABASE_USER=ci_user
DATABASE_PASSWORD=ci_pass
```

Point `DATABASE_HOSTNAME` at your MySQL. From Docker on Linux/WSL that is usually `host.docker.internal`, not a Compose service named `db`.

## Product

### Publish

- **Pages** — create, edit, preview, status (`0` deleted · `1` published · `2` draft · `3` archived). Private and scheduled pages are honoured on the public site.
- **Collections** — structured types (schema + items) rendered in the theme with `get_collection()`. Presets: portfolio, cards, team, FAQ. Admin URL remains `/admin/custommodels/` (historical).
- **Fragments** — reusable HTML, cached.
- **Menus, categories** — navigation and taxonomy.
- **Events** — start/end, place, slug. Public `/events` and `/events/{slug}`. Admin calendar uses event dates.
- **Albums and videos** — galleries and YouTube embeds.
- **Page embeds** — from New/Edit Page, insert a published form, fragment, menu, album, video, event, or collection as `{{helper(name)}}`. Expanded on the public site (whitelist only).

### Capture

- **Site forms** — public forms, submissions inbox, CSV export, submit cooldown.
- **Analytics** — sessions, pages, referrers, conversions. Client script + admin dashboard.
- **Notifications** — per-user inbox, bell in the admin bar, emitters from real CMS events (forms, users, backups).

### Operate

- **People** — users and groups with permission keys (`SELECT_PAGE`, `UPDATE_CONFIG`, …).
- **Files** — media explorer and uploads.
- **Settings** — general, appearance/theme, SEO, integrations, system, updater, data (backups / import / export), logs.
- **Search** — command palette across admin.
- **API** — `/api/v1/{resource}` with JWT or session token. Postman: `docs/api/postman-collection.json`.
- **Backups** — cron or pseudo-cron from the admin. See [docs/AUTOMATIC_BACKUPS.md](docs/AUTOMATIC_BACKUPS.md).

Admin UI: Vue 2 islands + Blade, tokens in `docs/DESIGN.md`, dark mode via `html.dark-mode`.

## Stack

| Layer | Choice |
|---|---|
| Runtime | PHP **7.4** (not 8+), Apache, CodeIgniter **3.1** |
| Templates | BladeOne (admin + themes) |
| Admin JS | Vue **2** globals, jQuery, Materialize — no bundler for Vue |
| CSS | SCSS → Vite → `public/css/admin/*.min.css` |
| Data | MySQL **5.7**, REST `/api/v1`, JWT |
| Editor | Trumbowyg (page HTML) |

Sources of truth: `application/` (PHP), `resources/components/` and `resources/js/` (admin JS), `resources/scss/admin/` (SCSS), `application/database/start.sql` (schema). Do not edit `vendor/`, `public/vendors/`, or compiled `public/css/admin/*.min.css`.

## Documentation

| Doc | What it is |
|---|---|
| [docs/README.md](docs/README.md) | Index |
| [docs/DOCKER.md](docs/DOCKER.md) | Container, host MySQL, ports |
| [docs/BUILD.md](docs/BUILD.md) | Vite, `npm run watch` |
| [docs/DESIGN.md](docs/DESIGN.md) | Admin tokens and UX |
| [docs/COLLECTIONS.md](docs/COLLECTIONS.md) | Collections (shipped) |
| [docs/PAGE_EMBEDS.md](docs/PAGE_EMBEDS.md) | Page helper tokens (shipped) |
| [docs/EVENTS_CORE_PLAN.md](docs/EVENTS_CORE_PLAN.md) | Events public loop (shipped) |
| [CHANGELOG.md](CHANGELOG.md) | 3.0 and earlier notes |

Agent map: `AGENTS.md`. Extra schema: `application/database/migrations/`.

## Scripts

```bash
npm run build      # copy resources/js → public/js, compile SCSS
npm run watch      # watch JS copy + SCSS
npm run test       # Vue admin P0 contracts

./bin/install.sh        # start ci_php56 (MySQL must already be up)
./bin/validate-env.sh
./bin/backup-db.sh
./bin/auto_backup.sh
./bin/docker_backup.sh
./bin/server.sh         # PHP built-in server (no Docker)
```

Inside the container:

```bash
docker exec -it ci_php56 bash
docker exec ci_php56 composer install
docker logs -f ci_php56
```

Backup/restore is against **host** MySQL, not a Compose `db` service. Example:

```bash
mysqldump -h 127.0.0.1 -u ci_user -pci_pass start_cms_db > backup.sql
```

## Project layout

```
application/     PHP app (controllers, models, Blade admin)
public/          Built CSS, copied JS, vendors
resources/       SCSS, Vue components, start.js
themes/          Public site themes
uploads/         User files (not the source of truth for DB rows)
docs/            Product and implementation docs
bin/             Install, backup, watch, QA
```

## Troubleshooting

**Admin 401 / blank login** — confirm `ci_php56` is up (`docker ps`) and `.env` `APP_BASE_URL` matches the URL you open (including trailing slash).

**Database connection error** — MySQL is on the host. From the container use `host.docker.internal`. The old Compose service name `db` is gone. Check `.env` and that `start_cms_db` exists.

**Styles stale after SCSS edits** — run `npm run build` or `npm run watch` in this tree. Do not hand-edit `public/css/admin/*.min.css`.

**Worktree preview** — `:8081` is the **main** checkout. A feature worktree needs its own Apache on `8082–8099`. See `.cursor/rules/worktree-preview.mdc`. Never `docker compose up` from a worktree (it would replace `ci_php56`).

## License

See [LICENSE.txt](LICENSE.txt). `composer.json` / `package.json` version: **3.0.0**.

## Contribute

Open an issue or PR on GitHub. One feature per branch/worktree. Do not “fix” historical identifiers (`permisions`, `categorie`, `albumes`, `fragmentos`, `patern`) — they are data and URL contracts.
