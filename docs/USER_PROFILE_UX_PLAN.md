# Plan técnico: UX perfil admin `/admin/users/ver/:id`

**Rama:** `feat/admin-user-profile-ux`  
**Worktree:** `/home/gervis/.cursor/worktrees/startCodeIgniter-CSM/admin-user-profile-ux`  
**Preview aislada:** `http://localhost:8084/` (contenedor `ci_php_feat_admin_user_profile`)  
**DB:** `start_cms_db` (compartida con `:8081`)  
**Checkout principal:** no tocar. No `docker compose`. No `ci_php56`. No Cloud Agents.

Stack: PHP 7.4 + CI 3.1 + Vue 2 global (sin bundler para JS de página) + BladeOne + Materialize + SCSS en `resources/scss/admin/`. Prohibido PHP 8+ (`match`, union types, named args, `?->`, enums). Typos históricos (`permisions`, `categorie`) no “corregir”.

Fuentes: `docs/DESIGN.md`, `.cursor/rules/admin-ux.mdc`, `AGENTS.md`.

No confundir con el worktree viejo `feat/user-timeline-pagination`.

---

## 0. Entorno de ejecución (obligatorio)

1. Cwd = worktree. `git branch --show-current` = `feat/admin-user-profile-ux`.
2. Preview: `http://localhost:8084/` cookie `ci_session_feat_admin_user_profile`. Login `gerber` / `admin123`. Perfil: `/admin/users/ver/1`.
3. Si el contenedor no corre, receta en `.cursor/rules/worktree-preview.mdc` (puerto **nunca** 8081). Hoy ya está en **8084**.
4. Tras editar SCSS: `npm run build` **en el worktree**. JS de `resources/components/*.js` se sirve directo (no copiar a `public/js/components/`).
5. `uploads/` es del worktree; filas `file` son de la DB compartida (thumbs pueden 404).
6. No migraciones `DROP`/`ALTER` destructivas contra `start_cms_db`.

---

## 1. Objetivo de producto

Rediseñar el perfil para que un editor/admin pueda:

- Identificar a la persona (avatar, bio, cargo, contacto, estado).
- Ver **qué creó** (actividad compacta + infinite scroll).
- Ver **qué hizo** (logger paginado).
- Ver **con qué cuenta opera** (grupo, permisos, fechas).
- Ver **volumen de trabajo** (KPI + desglose + borradores + archivos recientes).
- **Inactivar / activar / borrar** a otros usuarios si tiene permisos (nunca self).

Una acción primaria visual: menú `more_vert` del banner. Tabs no son CTAs filled.

---

## 2. Layout

```
.user-profile
  col s12 m5 l4  .user-profile-aside (sticky desktop)
    card banner (avatar, nombre, username, role, last seen, status badge, more_vert)
    collection contacto (email/tel/dir; ocultar vacíos)
    bio + cargo si hay valor
  col s12 m7 l8  .user-profile-main
    .user-profile-kpis (4 KPI)
    ul.tabs#user-tabs
      #activity  drafts card + timeline compacto + sentinel
      #logs      logger list + sentinel
      #account   grupo, permisos chips, fechas, last login, desglose, files rail
```

- Tabs ids semánticos `#activity` `#logs` `#account`. Prohibido `#test1`.
- Sticky aside: `position: sticky; top: calc(var(--st-navbar-height) + 1rem); align-self: flex-start;` solo `min-width` desktop (sidenav breakpoint 992px).
- Dark mode: `html.dark-mode` + `var(--st-*)`. Cero hex AdminLTE (`#dd4b39`, `#0073b7`, `#f39c12`, `#d2d6de`).
- Radio `--st-radius-md`. Iconos Material Icons. Cero `<style>` en Blade. Cero `style=""` salvo excepción FAB ya existente (aquí no hay FAB).

---

## 3. Inyección PHP → JS (no parsear URL)

Hoy `UserProfileComponent.js` hace `window.location.pathname.split("/")[4]` (rompe con `index.php` o subdir).

En [`user_profile.blade.php`](application/views/admin/user/user_profile.blade.php) **antes** del Vue:

```javascript
window.USER_PROFILE = {
  userId: <int>,
  isSelf: <bool>,
  canUpdate: <bool>,   // has_permisions('UPDATE_USER') || isSelf
  canDelete: <bool>,   // has_permisions('DELETE_USER') && !isSelf
  canDeactivate: <bool>, // has_permisions('UPDATE_USER') && !isSelf
  canSelectFiles: <bool>,
  canSelectConfig: <bool>,
  canUpdateUsergroup: <bool>,
  loggerEnabled: <bool>, // (string)config('SYSTEM_LOGGER') === '1'
  groupEditUrl: <string|null>
};
window.ADMIN_LANG = Object.assign({}, window.ADMIN_LANG || {}, { /* keys perfil */ });
```

`userId` sale de `$user->user_id` del `ver()`. Avatar explorer: `multiple=false`, `filter=images`, `initialdir` `./public/img/profile/{username}/`.

Quitar de esta vista CSS/JS de `fileinput` (no se usa; el picker es `file-explorer-selector`).

---

## 4. Backend

### 4.1 Auth helper compartido (UsersController API)

Añadir métodos protected (PHP 7.4, arrays cortos ok):

- `can_access_user_profile($user_id)`: true si `(int)$user_id === (int)userdata('user_id')` **o** `has_permisions('SELECT_USERS')`. Si false → 403 `lang('not_have_permissions')`.
- `assert_can_mutate_other_user($target UserModel)`:
  - 403 si target `user_id` === sesión (no self-delete / self-deactivate).
  - 403 si `(int)$target->usergroup_id < (int)userdata('usergroup_id')` (no tocar a alguien más privilegiado; mismo criterio que `get_full_info`: `usergroup_id >= sesión`).
- `pagination_from_get()`: `page` default 1, `per_page` default 20, max 100. Devolver `current_page, per_page, offset, …` alineado a `MY_Model::build_pagination_info`.

Usar `$this->response_ok($data, $paginationMeta)` / `respond_collection` existentes. Vue espera `response.code == 200` y `response.data`.

### 4.2 `UserModel::get_full_info($user_id = null, $options = array())`

Archivo: [`application/models/Admin/UserModel.php`](application/models/Admin/UserModel.php).

Hoy: `WHERE u.status = 1` + concat SQL de `$user_id` (inyección). Cambios:

- Bind: `u.user_id = ?` con `(int)$user_id`.
- Bind `usergroup_id >= ?` con `(int)userdata('usergroup_id')`.
- Si `$options['include_inactive']` truthy **o** el caller es admin viendo un id concreto: no filtrar `status = 1`. Para listados (`get_full_info()` sin id) **seguir** filtrando activos salvo que se pida lo contrario (el listado de users ya tiene su propio query; no cambiar el listado en esta PR).
- `ver()` y `index_get($id)` deben encontrar status 0.

`profile_user_data()` ya rellena `nombre, apellido, telefono, direccion, avatar, cargo, bio`. Usarlo también al serializar API si hace falta.

### 4.3 `index_get($user_id)` — self

Hoy exige `SELECT_USERS` siempre. Alinear: si hay `$user_id` y es self, no exigir `SELECT_USERS`. Lista completa sí.

### 4.4 Timeline paginado

Reemplazar `get_timeline()` (merge `all()` + `usort` + nested `function sortFunction`).

Nueva firma:

```php
public function get_timeline($user_id, $limit = 20, $offset = 0)
public function count_timeline($user_id)
```

SQL `UNION ALL` (MySQL 5.7), **tres placeholders** de `user_id`:

| model_type | tabla | entity_id | title | extra |
|---|---|---|---|---|
| `page` | `page` | `page_id` | `title` | `path`, `page_id`, `status` |
| `custom_model` | `custom_model` | `custom_model_id` | `form_name` | `custom_model_id` |
| `custom_model_content` | `custom_model_content` LEFT JOIN `custom_model` | `custom_model_content_id` | `c.title` | `custom_model_id`, `collection_name` = `form_name` |

Filtro: `user_id = ? AND status != 0`.  
`ORDER BY date_create DESC` sobre el union.  
`LIMIT ? OFFSET ?` (enteros casteados).

**No** seleccionar `content`, `user`, `imagen_file`, ni hidratar relaciones.

Payload ítem:

```json
{
  "entity_id": 68,
  "model_type": "page",
  "title": "Collections",
  "date_create": "2020-09-13 23:31:39",
  "status": "1",
  "page_id": 68,
  "path": "collections",
  "custom_model_id": null,
  "custom_model_content_id": null,
  "collection_name": null
}
```

`GET /api/v1/users/timeline/:id?page=1&per_page=20`  
Método: `timeline_get($user_id)`. Auth: `can_access_user_profile`. 404 si el user no existe (`find` **unfiltered** status, o `get_full_info` con inactive).

Respuesta: `data: [...]` + keys de paginación al mismo nivel que `respond_collection` (`current_page`, `per_page`, `total_rows`, `total_pages`).

Ruta ya cubierta: `$route['api/v1/users/(.+)']`.

### 4.5 Logs paginados

`GET /api/v1/users/logs/:id?page=&per_page=` → `logs_get($user_id)`.

**No** usar `LoggerModel::filter_results()` (N+1 `UserModel::find` + PageModel). Query builder:

```php
$this->db->from('logger');
$this->db->where('user_id', (int)$user_id);
$this->db->order_by('date_create', 'DESC');
$this->db->limit($per_page, $offset);
```

Campos: `logger_id, type, type_id, token, comment, date_create`.

`type_link` en PHP (sin queries extra):

| type | link |
|---|---|
| `pages` | `admin/pages/editar/{type_id}` |
| `users` | `admin/users/ver/{type_id}` |
| `custom_model` | `admin/custommodels/items/{type_id}` |
| `custom_model_content` | `admin/custommodels/` (o items si se conoce model; si no, sin link) |
| `config` / `site_config` | `admin/configuracion/` |
| default | `null` |

Auth: `can_access_user_profile`. No exigir `SELECT_CONFIG`.

Incluir en la respuesta (una vez): `logger_enabled: bool` para el empty state, **o** el front usa `USER_PROFILE.loggerEnabled`.

### 4.6 Summary (KPI + widgets)

`GET /api/v1/users/summary/:id` → `summary_get($user_id)`. Auth: `can_access_user_profile`.

COUNTs con query builder / SQL bound. Tablas whitelist (typo `fragmentos` y `events` reales):

| key | table | extra where |
|---|---|---|
| pages | `page` | status != 0 |
| collections | `custom_model` | status != 0 |
| items | `custom_model_content` | status != 0 |
| files | `file` | status != 0 |
| drafts | `page` | status = 2 |
| fragments | `fragmentos` | status != 0 |
| albums | `album` | status != 0 |
| events | `events` | status != 0 |
| menus | `menu` | status != 0 |
| siteforms | `siteform` | status != 0 |

Helper interno `count_by_user($table, $user_id, $where)` con `$table` solo del whitelist.

`drafts`: `SELECT page_id, title, date_update FROM page WHERE user_id=? AND status=2 ORDER BY date_update DESC LIMIT 5`.

`recent_files`: `SELECT file_id, file_name, file_path, file_type, date_create FROM file WHERE user_id=? AND status!=0 ORDER BY file_id DESC LIMIT 8`. Front arma URL: `BASEURL + file_path.replace(/^\.\//,'') + file_name + '.' + file_type`. Imagen si `file_type` in `jpg,jpeg,png,gif,webp,svg` (case insensitive).

`last_login`: `SELECT date_create FROM logger WHERE user_id=? AND token='login' ORDER BY logger_id DESC LIMIT 1` o null.

`permissions`: `UsergroupModel::usergroup_permisions()` del `usergroup_id` del target (array de `permision_name`). No N+1 de users.

`role` / `usergroup_id` / `usergroup_name` pueden ir aquí o salir del GET user.

### 4.7 Status (inactivar / activar)

`POST /api/v1/users/status` → `status_post()`.

Body: `user_id`, `status` (solo `0` o `1`).

Permiso: `UPDATE_USER`. Luego `assert_can_mutate_other_user`.

`find($user_id)` (incluir inactivos: `find` de MY_Model no filtra status=1 por PK). Set `$user->status = $status`. **No** `$user->delete()`, **no** `date_delete`. `save()`. `system_logger('users', $id, 'deactivated'|'activated', ...)`.

Login ya exige `status === 1` (`LoginModModel`, `LoginController`).

### 4.8 Delete

`index_delete($user_id)` ya exige `DELETE_USER` y `soft_delete` (status 0 + `date_delete`). Añadir `assert_can_mutate_other_user` (bloquea self y privilegio).

Front: `confirm-modal` + `DELETE api/v1/users/:id` (mismo patrón que `listMixin.deleteListItem` / users list). Redirect `admin/users`. Toast `toast_deleted`.

### 4.9 Admin `ver()`

[`application/controllers/admin/UsersController.php`](application/controllers/admin/UsersController.php):

- `get_full_info($id, array('include_inactive' => true))`.
- Menú: no PHP `Block`/`Delete` muertos. Pasar flags a Vue; construir dropdown en Blade **o** Vue con `v-if`.
  - Self: Edit, Change password (`lang('users_form_change_password')`), Change avatar.
  - Otro + `UPDATE_USER`: Edit, Change avatar, Inactivar **o** Activar según `status`.
  - Otro + `DELETE_USER`: Eliminar (abre modal).
- Copy 100% `lang()`. Quitar strings `'Change avatar'`, `'Block'`, `'Preview'`.
- `more_vert`: `aria-label` + tooltip.
- Avatar click → `#folderSelector` solo si `canUpdate`.
- `ver()` puede seguir exigiendo `SELECT_USERS` en `$routes_permisions` (navbar “Mi perfil” asume que gerber/root lo tiene). No reescribir `MY_Controller::check_permisions` en esta PR. Self-API sí se relaja (4.3).

---

## 5. Front Vue 2 — [`UserProfileComponent.js`](resources/components/UserProfileComponent.js)

`el: #root`, `mixins: [mixins]`. Sin `import`/`export`.

### 5.1 data

```javascript
{
  loader: true,
  user: new User(),
  summary: { counts: {}, drafts: [], recent_files: [], permissions: [], last_login: null },
  timelineGroups: {}, // { 'YYYY-MM-DD': [items] } insertar en orden desc
  timelinePage: 0,
  timelineHasMore: true,
  timelineLoading: false,
  logs: [],
  logsPage: 0,
  logsHasMore: true,
  logsLoading: false,
  logsLoaded: false,
  accountLoaded: false,
  activeTab: 'activity'
}
```

### 5.2 Carga

- `mounted`: `getUser()` + `getSummary()` + `loadTimelinePage()` + `initPlugins()` en `$nextTick`. **No** `setTimeout(4000)`.
- Tabs: `M.Tabs.init(#user-tabs, { onShow: fn })`. Al show `#logs` si `!logsLoaded` → primera página logs. Al show `#account` si `!accountLoaded` marcar true (summary ya vino).
- Infinite scroll: `IntersectionObserver` sobre `.js-timeline-sentinel` / `.js-logs-sentinel` (`root: null`, `rootMargin: '200px'`). Fallback botón `user_profile_load_more`. Desconectar observer si `!hasMore`.
- Agrupar timeline por `date_create.split(' ')[0]`. Preservar orden: recorrer páginas concatenando. Keys Vue: `model_type + '-' + entity_id`. **Prohibido** `makeid(5)`.
- Loader perfil: `loader` solo para GET user. Timeline/logs tienen spinners locales. Columna izquierda visible cuando `!loader`.
- `getUser` / fetch: `credentials: 'same-origin'`, `response.code == 200`. `catch` usa el parámetro correcto. Toasts: `this.toast('toast_error')` / keys nuevas. Prohibido `"Ocurrió un error inesperado"` suelto.

### 5.3 Actividad UI

Fila compacta (~64px): icono círculo token (`page` → `--st-interactive` + `web`; `custom_model` → `--st-warning` + `view_module`; `custom_model_content` → `--st-accent` + `view_list`) + título + meta (`lang` tipo + `timeAgo(date_create)`) + un `<a>`:

- page → `admin/pages/editar/{page_id}`
- custom_model → `admin/custommodels/items/{id}`
- custom_model_content → `admin/custommodels/items/{custom_model_id}` si hay id

Sin imagen, sin excerpt 220, sin `user-info` duplicado, sin dropdown por ítem.

Empty: icono `view_day` + `lang('user_profile_activity_empty')`.

**Drafts card** (v-if `summary.drafts.length`): título `user_profile_drafts`, lista 5, link editar. Sin drafts: no render.

### 5.4 Logs UI

Fila: chip `type` + `token` + comment truncado 140 + `timeAgo`. Si `type_link`, el título es `<a>`.

Empty logger off: `user_profile_logs_disabled` + link config si `canSelectConfig`.  
Empty logger on: `user_profile_logs_empty`.

### 5.5 Cuenta UI

- Grupo: `user.role` + link `admin/users/editGroup/{usergroup_id}` si `canUpdateUsergroup`.
- Chips permisos (`summary.permissions`): `.chip` texto `permision_name` (interno; copy visible puede ser el name). Read-only.
- Fechas: `date_create`, `lastseen` (`users_last_seen`), `last_login`.
- Desglose: filas con count > 0: fragments, albums, events, menus, siteforms. Links solo si `has_permisions` equivalente pasado en `USER_PROFILE` (añadir `canSelectFragments` etc. o un mapa `can: { fragments: bool, ... }`). Si count 0, ocultar fila.
- Files rail: si `canSelectFiles` y `recent_files.length`. Thumb o icono `insert_drive_file`. Click: `BASEURL + relative path`. Sin permiso: no rail (KPI files igual se muestra).

### 5.6 Acciones

- Avatar `uploadCallback`: un archivo, `ExplorerFile`, `POST api/v1/users/avatar` (ya existe). Cerrar modal.
- Inactivar: `POST api/v1/users/status` `{ user_id, status: 0|1 }`. Actualizar `user.status` local. Toast.
- Delete: `tempDelete` + `#deleteModal` `confirm-modal` (ver [`UserComponent.js`](resources/components/UserComponent.js) `onRequestDelete`). DELETE API. Redirect `BASEURL + 'admin/users'`.

Dropdowns: `M.Dropdown.init` en `$nextTick` **después** de que el banner esté en el DOM (`v-show=!loader`). Re-init si el menú cambia (status). No `updated()` loop. Mixin `initPlugins` ignora `#slide-out`.

### 5.7 Filtros rotos a borrar

- `shortDate` que hace `split("at")` (i18n frágil). Usar fecha agrupada `YYYY-MM-DD` formateada con `lang` o mostrar `timeAgo` solo en ítems.
- `self.users = []`.
- `:key="makeid(5)"`.
- `user. get_avatarurl()` espacio.

---

## 6. SCSS — [`resources/scss/admin/userprofile.scss`](resources/scss/admin/userprofile.scss)

`@import "components/palette";` ya está.

Añadir BEM:

- `.user-profile-kpis` — grid 4 cols, gap 1rem; en `phone-only` 2x2. Reutilizar visual de `.kpi-card` del dashboard **o** clases locales que copien tokens (no importar dashboard.scss entero si acopla). Números `font-weight: 600`. Clickable `button` o `<a class="kpi-card">` con focus ring `--st-interactive`.
- `.user-profile-timeline-item` — flex, icon 32px, sin card z-depth enorme.
- `.user-profile-files` — grid thumbs 48–64px, `object-fit: cover`, radius 4px.
- `.user-profile-aside` sticky.
- Banner overlay: bajar `z-index: 99999` a algo razonable (2–3) para no tapar dropdowns.
- Reemplazar `.bg-red/.bg-blue/...` usados por el timeline nuevo; se pueden dejar las clases viejas no usadas o borrar si nadie más las importa (este scss es entry Vite `userprofile.min`).
- Tabs: underline `--st-interactive` (no light blue Materialize).

`npm run build` genera `public/css/admin/userprofile.min.css`. No editar el min a mano.

---

## 7. i18n

Archivos: [`application/language/english/admin/users_lang.php`](application/language/english/admin/users_lang.php) + spanish espejo.

Claves mínimas (EN copy, tarea no jerga):

- `user_profile_tab_activity` Activity
- `user_profile_tab_logs` Activity log
- `user_profile_tab_account` Account
- `user_profile_activity_empty` No content yet
- `user_profile_logs_empty` No recorded actions
- `user_profile_logs_disabled` System logging is off
- `user_profile_logs_open_config` Open settings
- `user_profile_load_more` Load more
- `user_profile_drafts` Drafts
- `user_profile_kpi_pages` Pages
- `user_profile_kpi_collections` Collections
- `user_profile_kpi_items` Items
- `user_profile_kpi_files` Files
- `user_profile_type_page` Page
- `user_profile_type_collection` Collection
- `user_profile_type_item` Collection item
- `user_profile_deactivate` Deactivate
- `user_profile_activate` Activate
- `user_profile_change_avatar` Change photo
- `user_profile_last_login` Last login
- `user_profile_permissions` Permissions
- `user_profile_breakdown` Other content
- `user_profile_recent_files` Recent files
- `user_profile_status_active` Active
- `user_profile_status_inactive` Inactive
- `user_profile_deactivated` Account deactivated
- `user_profile_activated` Account activated
- `user_profile_confirm_delete` (o reusar `users_confirm_delete`)

Spanish: mismo set. Toasts reusar `toast_error`, `toast_deleted`, `toast_saved`.

Preview: `lang('btn_edit')` ya existe; no hardcode `Preview` (si se muestra preview de página: `lang` existente o `user_profile_open_page`). En cards compactas **no** hay preview.

Inyectar las keys usadas en JS vía `Object.assign` en el Blade (como [`form.blade.php`](application/views/admin/user/form.blade.php)).

---

## 8. Bugs P0 (checklist, no omitir)

| Bug | Fix |
|---|---|
| catch `getUser` referencia `error` | `(err)` + `toast_error` |
| catch timeline referencia `response` | igual |
| `self.users` | borrar |
| `makeid` keys | `model_type-entity_id` |
| dropdown 4s | `$nextTick` |
| explorer multiple | `false` |
| `href="#!"` contacto | `mailto:` / `tel:` / texto |
| Delete/Block muertos | API real / quitar Block |
| perfil 404 si status 0 | `include_inactive` |
| timeline carga todo + HTML content | UNION + payload chico |
| `custom_model_content` no se pinta | tipo en template compacto |
| fileinput muerto en perfil | quitar includes |

---

## 9. Archivos a tocar (lista cerrada)

**Cambiar**

- `application/models/Admin/UserModel.php`
- `application/controllers/api/v1/UsersController.php`
- `application/controllers/admin/UsersController.php`
- `application/views/admin/user/user_profile.blade.php`
- `resources/components/UserProfileComponent.js`
- `resources/scss/admin/userprofile.scss`
- `application/language/english/admin/users_lang.php`
- `application/language/spanish/admin/users_lang.php`

**Generados (build)**

- `public/css/admin/userprofile.min.css` (+ map)

**No tocar**

- `vendor/`, `public/vendors/`, `graphify-out/`, `public/js/components/`, checkout principal, `ci_php56`, compose, schema `start.sql` (no hace falta migración).

Rutas API nuevas no requieren `routes.php` extra (`users/(.+)` ya despacha).

---

## 10. Verificación (preview `:8084`, no `:8081`)

Login `gerber`/`admin123`.

1. `GET http://localhost:8084/api/v1/users/timeline/1?page=1&per_page=20` (cookie sesión) → ≤20 ítems, sin campo `content`.
2. `page=2` → siguiente lote o `data: []` + `total_pages`.
3. `GET .../users/summary/1` → `counts`, `drafts`, `recent_files`, `permissions`.
4. `GET .../users/logs/1?page=1&per_page=20` → filas logger, sin `user` hidratado enorme.
5. UI `/admin/users/ver/1`: KPI visibles; Activity no ocupa 20 pantallas; scroll carga más; drafts si hay status=2.
6. Tab Logs: red al primer click; empty coherente.
7. Tab Cuenta: chips + desglose + files.
8. Avatar self: un archivo, se persiste.
9. Otro usuario (crear uno Estandar si hace falta): Inactivar → logout de esa cuenta falla login; perfil de gerber sigue abriendo `/ver/{id}` con badge Inactive; Activar.
10. Delete con modal → lista users. Self: no Delete/Inactivar; sí password.
11. Dark mode: texto `--st-text` legible.
12. `curl -I http://localhost:8084/admin/users/ver/1` (con cookie) no 500.

Si no hay browser MCP: documentar curls + `curl -I`.

---

## 11. Fuera de alcance

`user_tracking`, `api_logs`, inbox notificaciones, `site_config` en el perfil, cover, editar permisos inline, filtrar `/admin/pages` por `user_id`, PHP 8, Cloud Agents, `docker compose` en el worktree.

---

## 12. Entrega

Commit en `feat/admin-user-profile-ux` cuando el usuario lo pida. PR desde este worktree. Mensaje: por qué (perfil usable, timeline paginado, admin puede inactivar). No commitear `.env`.
