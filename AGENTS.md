# startCodeIgniter CSM — guía para agentes

**Start CMS 3.0.** CMS ligero sobre **CodeIgniter 3.1** (PHP **7.4**), BladeOne, REST + JWT, Vue 2 (globales, sin bundler) y Materialize. MySQL 5.7. Versión de producto: `ADMIN_VERSION` en `application/config/constants.php`. No uses sintaxis de PHP 8+ (`match`, union types, named arguments, nullsafe `?->`, enums).

## Arranque

```bash
docker compose up -d          # http://localhost:8081  |  admin: /admin/login
npm run build                 # SCSS → public/css/admin + copia JS
npm run watch                 # recompila SCSS
```

Credenciales por defecto: `gerber` / `admin123`. `.env` desde `.env.example` (MySQL suele ser `host.docker.internal`, no el servicio `db` — el compose actual solo levanta `web`).

## Worktrees (features en paralelo)

Aislamiento **local** (sin Cloud Agents). Al crear un worktree, Cursor corre `.cursor/setup-worktree-unix.sh` (vía `.cursor/worktrees.json`): copia `.env`, `vendor/`, `node_modules/` y `themes/` desde el checkout principal.

- Una feature = un chat = `/worktree`. No Cloud, no `/in-cloud`, no `/best-of-n` salvo que se pida.
- El principal es `ci_php56` en `http://localhost:8081` (volume = checkout principal). Un `docker compose up` en el worktree **pisa** ese contenedor y el puerto.
- Para probar la feature **antes** de merge: Apache aislado con `docker run` (puerto `8082+`, misma `start_cms_db`). Protocolo en `.cursor/rules/worktree-preview.mdc`.
- Tras merge o `/apply-worktree`, el principal sigue en `http://localhost:8081`.

## Mapa del repo

| Ruta | Rol |
|---|---|
| `application/controllers/admin/` | Páginas HTML del panel. Extienden `MY_Controller`. |
| `application/controllers/api/v1/` | REST. Extienden `REST_Controller`. Aquí vive el CRUD. |
| `application/controllers/PageController.php` | Front público. Extiende `Base_Controller`. |
| `application/core/MY_Controller.php` | `MY_Controller` (admin) + `Base_Controller` (sitio). |
| `application/core/MY_Model.php` | ORM propio. Todos los modelos heredan de aquí. |
| `application/models/Admin/` | Modelos de dominio. Cargar con `$this->load->model('Admin/PageModel')`. |
| `application/views/admin/` | Blade del admin (`admin.foo.bar` → `admin/foo/bar.blade.php`). |
| `application/config/routes.php` | Rutas admin + `api/v1/*`. |
| `resources/scss/admin/` | Fuente SCSS. No editar `public/css/admin/*.min.css`. |
| `resources/components/` | Fuente de Vue. El admin las carga desde aquí (`base_url('resources/components/...')`). |
| `resources/js/` | JS global (`start.js` define `mixins`). Copiar a `public/js/` con `npm run build`. |
| `themes/` | Temas del sitio público (`ThemeController` + vistas Blade). |
| `application/database/start.sql` | Schema + seed. Migraciones extra en `application/database/migrations/`. |
| `docs/` | Índice en `docs/README.md`. Docker, Vite, backups, colecciones, embeds, eventos. |

No editar: `vendor/`, `public/vendors/`, `graphify-out/`, `application/graphify-out/`, `application/cache/`, copias en `public/js/` (editar `resources/`).

## Cómo se parte una feature

El admin **no** persiste: pinta Blade + Vue. Vue habla con `/api/v1/{recurso}`.

1. Modelo en `application/models/Admin/` (`$table`, `$primaryKey`, `$hasOne`/`$hasMany`).
2. API en `application/controllers/api/v1/{Recurso}Controller.php` (`index_get/post/delete`, extra `{accion}_{http}`).
3. Ruta en `routes.php` (`api/v1/{recurso}` y `admin/{recurso}`).
4. Admin controller con `$routes_permisions` + `$this->check_permisions()` en el constructor + `renderAdminView()`.
5. Vista `@extends('admin.layouts.app')` y componente Vue en `resources/components/`.
6. Cargar el JS de la página en `@section('footer_includes')` del Blade (`resources/components/...`). Una sola vía: no inyectar el mismo archivo desde PHP.
7. Strings en `application/language/english/admin/common_lang.php` (y spanish si aplica).
8. Permiso `CREATE|UPDATE|DELETE|SELECT_{RECURSO}` en tabla `permisions`.

Ejemplo de referencia vertical: páginas = `PagesController` (admin + api) + `PageModel` + `pages_list.blade.php` + `PagesLists.js`.

## Convenciones que no “corregir”

Ortografía histórica en código, tablas y URLs (rompería datos y rutas):

- `permisions`, `categorie`, `albumes`, `fragmentos`, `usergroup_permisions`
- Métodos admin en español: `nueva`, `editar`, `agregar` (aliases EN en `routes.php`)
- `patern` (typo de pattern) en `$routes_permisions`

Hay controladores viejos sin sufijo (`Categories.php`, `Gallery.php`, `Fragments.php`). Preferí `*Controller.php` y las rutas ya mapeadas.

## Auth y permisos

- Admin HTML: sesión `logged_in` (redirige a `/admin/login`).
- API: JWT flaco `sub`+`exp` en `Authorization` (Bearer opcional) **o** sesión cookie. `verify_request()` en el constructor; si falla → 401.
- Permisos: `has_permisions('UPDATE_PAGE')`. Nombres en `permisions.permision_name`.
- Usuario actual: `userdata('user_id')`. Auditoría: `system_logger($type, $type_id, $token, $comment)`.
- Config del sitio (tabla `site_config`): `config('SITE_TITLE')`.

## Status (contenido)

`0` eliminado · `1` publicado · `2` borrador · `3` archivado. `MY_Model::all()` filtra `status = 1` por defecto.

## Graphify

Hay un grafo en `graphify-out/` (también `application/graphify-out/`). Para preguntas de arquitectura (“qué llama a X”, “flujo de páginas”), consultar el grafo antes de recorrer el repo a ciegas. Tras cambios grandes de PHP: `graphify --update`.

## Docs

Índice: `docs/README.md`. Producto y arranque: `README.md`.

- `docs/DESIGN.md` — tokens, patrones y deuda UX del admin
- `docs/DOCKER.md` · `docs/BUILD.md` · `docs/BACKEND_ENDPOINTS.md`
- `docs/COLLECTIONS.md` · `docs/PAGE_EMBEDS.md` · `docs/EVENTS_CORE_PLAN.md`
- `docs/api/postman-collection.json`
- `docs/AUTOMATIC_BACKUPS.md`
