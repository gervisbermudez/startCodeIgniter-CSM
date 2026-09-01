# Documentación — Start CMS 3.0

Índice para humanos y agentes. El mapa de código está en `AGENTS.md`. El posicionamiento y el arranque están en el `README.md` de la raíz.

**Versión de producto:** `3.0.0` (`ADMIN_VERSION` en `application/config/constants.php`).

## Arranque y plataforma

| Doc | Rol | Estado |
|---|---|---|
| [../README.md](../README.md) | Producto, features 3.0, quick start | Actual |
| [DOCKER.md](DOCKER.md) | `ci_php56`, puerto 8081, MySQL en el host | Actual |
| [BUILD.md](BUILD.md) | Vite (SCSS), copy de `resources/js`, Vue sin bundler | Actual |
| [../CHANGELOG.md](../CHANGELOG.md) | 3.0 y notas 2.x | Actual |
| [../AGENTS.md](../AGENTS.md) | Cómo partir una feature, convenciones, worktrees | Actual |


## Producto (shipped)

Estos archivos nacieron como spec de implementación. El corte ya está en `master`. Sirven para entender el contrato; no para reimplementar el módulo.

| Doc | Módulo |
|---|---|
| [COLLECTIONS.md](COLLECTIONS.md) | Colecciones (`get_collection`, `/admin/custommodels`) |
| [PAGE_EMBEDS.md](PAGE_EMBEDS.md) | Tokens `{{helper(name)}}` en páginas |
| [EVENTS_CORE_PLAN.md](EVENTS_CORE_PLAN.md) | Eventos: fechas, slug, `/events` |
| [NOTIFICATIONS_INBOX_PLAN.md](NOTIFICATIONS_INBOX_PLAN.md) | Inbox por usuario + campana |
| [ADMIN_NAVBAR_CONTEXTUAL.md](ADMIN_NAVBAR_CONTEXTUAL.md) | Admin bar en el sitio público |
| [BACKEND_ENDPOINTS.md](BACKEND_ENDPOINTS.md) | Cache y acciones de esa barra |
| [AUTOMATIC_BACKUPS.md](AUTOMATIC_BACKUPS.md) | Cron y pseudo-cron |
| [ANALYTICS_USAGE_EXAMPLES.md](ANALYTICS_USAGE_EXAMPLES.md) | Tracking y dashboard |
| [USER_TRACKING_IMPROVEMENTS.md](USER_TRACKING_IMPROVEMENTS.md) | Schema y librería de analytics |
| [FRAGMENTS_MODULE_PLAN.md](FRAGMENTS_MODULE_PLAN.md) | Fragments: token, preview, chips, permisos |
| [USERGROUPS_ACCESS_PLAN.md](USERGROUPS_ACCESS_PLAN.md) | Grupos y permisos: matriz por módulo + enforcement |

## Diseño

| Doc | Rol |
|---|---|
| [DESIGN.md](DESIGN.md) | Tokens, patrones y deuda UX del **admin** (el tema público no comparte paleta) |
| [CMS_PRODUCT_COPY_PLAN.md](CMS_PRODUCT_COPY_PLAN.md) | Spec en curso: voz de producto, page-intro, copy híbrido editor/dev (`feat/cms-product-copy`) |

## API y datos

- Colección Postman: [api/postman-collection.json](api/postman-collection.json)
- Schema seed: `application/database/start.sql`
- Migraciones 3.0: `003_analytics_module.sql`, `005_collections.sql`, `006_notifications_inbox.sql`, `007_events_core.sql`, `008_siteforms_permissions.sql`, `009_albums_videos_cut_a.sql`, `010_fragments_cut_b.sql`, `011_usergroups_access.sql`, `012_usergroups_access_crud.sql`

## Notas viejas

- [devnotes.md](devnotes.md) — arranque actual (Docker + Vite). El flujo `gulp` / `php -S :8000` quedó atrás.
