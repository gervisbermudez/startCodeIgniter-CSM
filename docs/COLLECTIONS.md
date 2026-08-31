# Colecciones (Custom Models) — spec de implementación

Documento para un agente que implemente el corte de producto acordado.

**Stack:** PHP 7.4, CodeIgniter 3.1, BladeOne, Vue 2 global, Materialize. Sin PHP 8+ (`match`, `?->`, union types, named arguments).  
**Worktree:** un chat = `/worktree` local. No Cloud Agents. No `docker compose` en el worktree. Verificar UI en `http://localhost:8081` tras merge o `/apply-worktree`.  
**Guías:** `AGENTS.md`, `docs/DESIGN.md`, `.cursor/rules/`.

Leer este archivo entero antes de tocar código. No ampliar alcance.

---

## 1. Objetivo de producto

El módulo deja de ser “models / forms” y pasa a ser **colecciones**: tipos de contenido estructurado (schema) + N entradas + **render en el tema**.

Job: el editor define 2–5 colecciones del sitio (portfolio, equipo, FAQ, cards) y el tema las pinta con `{!! get_collection('home_portfolio') !!}`, igual que Site Forms usa `render_form()`.

No es page builder. No es formulario de contacto. No es fragmento HTML.

### 1.1 En alcance (v1)

1. Brand e IA: menú, dashboard, listas, copy.
2. Contrato de publicación: slug + template + helper de tema.
3. Lista de **entradas por tipo** (no un dump global como flujo principal).
4. Identidad de entrada: título, orden, destacado, status real.
5. Persistencia: una entrada = una fila; update de schema no destructivo.
6. Plantillas de render (`portfolio`, `cards`, `team`, `faq`, `default`).
7. Presets al crear tipo (campos precargados). Opcional si el tiempo aprieta: al menos el preset vacío + template `default`.

### 1.2 Fuera de alcance (v1)

- Ficha pública con URL propia (`/portfolio/start-cms`).
- Nuevos tipos de campo (repeater, relation, geo, archivos múltiples).
- Renombrar tablas, clases PHP, rutas canónicas o claves de `permisions`.
- Fusionar con Site Forms o Fragments.
- API pública sin auth (el tema consume server-side).
- Autoguardado, versionado de schema, i18n de campos.

---

## 2. Brand: qué cambia a la vista, qué no se toca

### 2.1 Conservar (contrato técnico)

| Superficie | Valor actual | Motivo |
|---|---|---|
| URLs admin | `/admin/custommodels/...` | No romper bookmarks ni permisos `patern` |
| API | `/api/v1/models/...` | Vue y Search ya apuntan aquí |
| Tablas | `custom_model`, `custom_model_tabs`, `custom_model_fields`, `custom_model_fields_data`, `custom_model_content`, `custom_model_content_data` | Datos existentes |
| Clases | `CustomModelsController`, `ModelsController`, `CustomModelModel`, etc. | Menos riesgo |
| Permisos (claves) | `SELECT_FORM_CUSTOMS`, `CREATE_FORM_CUSTOM`, `UPDATE_FORM_CUSTOM`, `SELECT_CONTENT_DATA`, `CREATE_CONTENT_DATA`, `UPDATE_CONTENT_DATA` | Filas históricas en `permisions` |
| Icono sidenav | `view_module` | Ya está en `DESIGN.md` |

Copy visible **sí** cambia. Claves internas **no**.

### 2.2 Vocabulario visible (un idioma por pantalla)

| Antes (UI) | EN | ES |
|---|---|---|
| Models / Modelos | Collections | Colecciones |
| Model / Form | Collection | Colección |
| Form name | Name | Nombre |
| Add Data / Add content | Add item | Añadir ítem |
| Contents / Contenidos | Items | Ítems |
| Form Type | Collection | Colección |
| Enable Model | Enabled | Activa |
| Latest Contents | Latest collection items | Últimos ítems |

Slug interno del tipo (nuevo campo): se muestra como “Slug” con ayuda al estilo Site Forms: *argumento de `get_collection()`*.

Site Forms sigue siendo “Forms” / “Formularios”. No reutilizar “form” en copy de colecciones.

### 2.3 Sidenav (`application/views/admin/shared/sidenav.blade.php`)

Sección `isSectionActive('custommodels')`:

- Header: `lang('menu_collections')` (reemplaza `menu_models`). Icono `view_module`.
- Hojas:
  1. `lang('menu_all')` → `/admin/custommodels/` (lista de **tipos**).
  2. `lang('menu_new')` → `/admin/custommodels/new`.
  3. Quitar “Contents” como tercer ítem de primer nivel **o** dejarlo como `lang('menu_all_items')` al final (“Todos los ítems”), secundario. Preferido: **quitarlo del sidenav**. El flujo principal es tipo → ítems.

Marcar current también en `editForm*`, `addData*`, `editData*`, `items*`.

### 2.4 Dashboard

**FAB principal** (`dashboard.blade.php` ~L587): hoy apunta a `admin/custommodels/new` con tooltip `tooltip_new_form` (“New Form”). Cambiar:

- Primaria: `/admin/pages/new/` + `lang('tooltip_new_page')`. Clase accent (`var(--st-accent)`), no `red` Materialize.
- Speed-dial: página, usuario, **colección** (`tooltip_new_collection` → `/admin/custommodels/new`), resto igual.

Alineado con `docs/DESIGN.md` § Dashboard.

**Widget `create-contents`:**

- Título: `lang('dashboard_latest_collection_items')`.
- Subtítulo: `{{ n }} lang('dashboard_collection_items_total')`.
- Badge: nombre de la colección (`form_name`), no “form”.
- Switch Active/Inactive → Publicado / Borrador vía `lang()`.
- CTA “ver todos” → lista de colecciones, no `/content` global si se retira.
- Props JS: se puede dejar `forms_types` internamente; copy visible no dice “forms”. Payload API: añadir alias `collections` = misma lista que `forms_types` (mantener `forms_types` un release para no romper el Vue viejo).

**Creator rail:** no añadir modo “colección”. Sigue page / album / categorie / fragment.

### 2.5 Búsqueda, calendario, perfil

- `SearchPalette.js`: labels de grupo `models` / `contents` → `lang` “Collections” / “Items”. Href de tipo: `editForm`. Href de ítem: `editData`. Título de ítem: `content.title` (nuevo campo), fallback al actual.
- `CalendarList.js`: dejar de apuntar a `custommodels/editar/` e `items/` rotos. Tipo → `editForm/{id}`. Ítem → `editData/{modelId}/{contentId}`. Títulos sin emoji de formulario.
- Perfil (`user_profile.blade.php`): “Add data” / `admin/CustomModels/addData` (clase mal capitalizada) → `admin/custommodels/items/{id}` o `addData`, todo por `lang()`.

### 2.6 Títulos de página admin

| Vista | `$h1` / title |
|---|---|
| Lista de tipos | Collections / Colecciones |
| Nuevo tipo | New collection / Nueva colección |
| Editar schema | Edit collection / Editar colección |
| Ítems del tipo | `{Name}` — items |
| Alta / edición de ítem | New item / Edit item |

Un idioma: las claves van en EN y ES (`admin_lang.php` + `spanish/admin/admin_lang.php`). Nada hardcodeado en Blade/Vue (“Model Name”, “Desription”, “Formulario Guardado”).

---

## 3. Modelo de datos

Status global del CMS (no cambiar semántica): `0` eliminado · `1` publicado · `2` borrador · `3` archivado. `MY_Model::all()` filtra `status = 1`.

Hoy el switch Enable/Disabled del tipo guarda `1`/`0`. **0 es delete.** Eso se corrige.

### 3.1 `custom_model` (tipo)

Añadir columnas (migración `005_collections.sql` + mismo delta en `application/database/start.sql` para installs nuevas):

```sql
ALTER TABLE `custom_model`
  ADD COLUMN `slug` varchar(120) NULL AFTER `form_name`,
  ADD COLUMN `template` varchar(80) NOT NULL DEFAULT 'default' AFTER `form_description`,
  ADD COLUMN `title_field` varchar(80) NULL AFTER `template`;

-- backfill
UPDATE `custom_model`
SET `slug` = LOWER(REPLACE(REPLACE(TRIM(`form_name`), ' ', '_'), '-', '_'))
WHERE `slug` IS NULL OR `slug` = '';

-- seed conocido
UPDATE `custom_model` SET `slug` = 'card', `template` = 'cards', `form_name` = 'Cards'
  WHERE `custom_model_id` = 22;
UPDATE `custom_model` SET `slug` = 'home_portfolio', `template` = 'portfolio'
  WHERE `custom_model_id` = 25;
```

Índice único de `slug` **solo entre no borrados**. MySQL 5.7 no tiene unique parcial: validar unicidad en PHP (`find_with(['slug' => $slug])` excluyendo `status = 0` y el propio id). No hace falta UNIQUE en SQL si choca con filas status 0; documentar la regla en el modelo.

`form_name` = **nombre humano**. No renombrar la columna.

`title_field` = Api ID del campo usado como título de listado (ej. `title`, `card_title`). Si es null, el save de ítem toma el primer campo cuyo `component` sea `formFieldTitle`, si no el primer Api ID.

`template` ∈ `{default, portfolio, cards, team, faq}` o cualquier `.blade.php` encontrado en el directorio de templates (como Site Forms).

**Status del tipo:**

| UI | `status` |
|---|---|
| Activa (se lista y se renderiza) | `1` |
| Desactivada (no `get_collection`, no `all()`) | `3` archivada |
| Eliminada | `0` (soft delete existente) |

El switch del form de schema: Enabled → `1`, Disabled → `3`. Nunca escribir `0` desde ese switch.

### 3.2 `custom_model_content` (entrada)

```sql
ALTER TABLE `custom_model_content`
  ADD COLUMN `title` varchar(250) NULL AFTER `custom_model_id`,
  ADD COLUMN `sort_order` int(11) NOT NULL DEFAULT 0 AFTER `title`,
  ADD COLUMN `featured` tinyint(1) NOT NULL DEFAULT 0 AFTER `sort_order`,
  ADD COLUMN `date_publish` timestamp NULL DEFAULT NULL AFTER `date_update`;
```

`form_tab_id` **se queda** (no DROP en v1). Deja de usarse como identidad:

- Al crear ítem: un solo `INSERT` en `custom_model_content`. Guardar `form_tab_id = 0` (o el primer tab id si hace falta NOT NULL; hoy es `NOT NULL DEFAULT 0` — usar `0`).
- No crear una fila por tab.

Backfill `title`: en PHP de migración o SQL best-effort desde `custom_model_content_data` no es fiable (JSON heterogéneo). Aceptable: `title` vacío hasta el próximo save; la lista usa fallback `Collection #{id}`.

`sort_order`: 0 por defecto; al listar `ORDER BY featured DESC, sort_order ASC, custom_model_content_id DESC`.

`featured`: `0`/`1`.

Status ítem: publicado `1`, borrador `2`. El switch del content form deja de guardar `0` para “draft”.

### 3.3 Campos / tabs

Sin cambio de tablas. Cambio de **comportamiento** en `CustomModelModel::update_form()`:

- Tabs existentes: `UPDATE` por `custom_model_tab_id`.
- Tabs nuevos: `INSERT`.
- Tabs quitados: soft o hard delete **solo si no hay content data**; si hay, rechazar o archivar el tab y dejar fields (v1: no borrar tabs/fields con data; toast de error).
- Fields existentes: `UPDATE` `custom_model_fields` + upsert `custom_model_fields_data` por `_key`. **Prohibido** `DELETE FROM custom_model_tabs WHERE custom_model_id = …` y recrear.

`save_form()` (alta) puede seguir insertando tabs/fields nuevos.

### 3.4 `CustomModelContentModel::save_data_form()`

Hoy itera tabs y hace un insert de content **por tab**. Corregir:

1. Un insert de `custom_model_content` (title, sort_order, featured, status, user_id, custom_model_id).
2. Por cada field de **todos** los tabs: insert en `custom_model_content_data`.
3. Derivar `title` desde `title_field` / primer title.

`update_data_form()`: update de la misma fila + update de data por `custom_model_fields_id`. Si el schema añadió un field nuevo, insertar la fila de data que falte.

---

## 4. Render público

Patrón de referencia: `render_form()` en `application/helpers/general_helper.php` + templates `site.templates.forms.{template}`.

### 4.1 Helpers (`general_helper.php`)

```php
/**
 * HTML de la colección publicada (status tipo = 1, ítems status = 1).
 * Si el slug no existe o está inactivo, string vacío (no 404).
 */
function get_collection($slug, $options = array())
```

`$options` (todos opcionales, PHP 7.4 array asociativo):

- `limit` int
- `featured` bool|null (true = solo destacados)
- `template` string override

Comportamiento:

1. `CustomModelModel::find_with(['slug' => $slug, 'status' => 1])`.
2. Cargar ítems publicados, ordenados, hidratar `fields` por Api ID (sin N+1: un query de content + data, no `where()` por field).
3. Normalizar cada ítem (ver §4.3).
4. Resolver vista: tema `themes/{theme}/views/site/templates/collections/{template}.blade.php` si existe; si no, fallback `application/views/site/templates/collections/{template}.blade.php`. Si tampoco, `default`.
5. `blade->changePath` como `render_form()`.
6. Return HTML string.

```php
function get_collection_items($slug, $options = array())
```

Igual pero return array de ítems normalizados (para un tema que quiera markup propio). No HTML.

Cache: opcional v1. Si se cachea, invalidar al save/delete de tipo o ítem. Clave `collection_{slug}`. Sin cache está bien en v1.

### 4.2 Snippet en admin

En el form de schema (y en la card del tipo), igual que Site Forms:

```
{!! get_collection('home_portfolio') !!}
```

Input readonly + botón copiar. `lang('collections_snippet_help')`.

### 4.3 Shape del ítem para templates

Cada ítem que llega a Blade:

```php
(object) array(
    'id' => 41,
    'title' => 'Start CMS',
    'featured' => false,
    'sort_order' => 0,
    'date_publish' => '...',
    'fields' => array(
        'title' => 'Start CMS',          // escalar
        'card_content' => '...',
        'image' => (object) array(        // si el campo es imagen
            'url' => 'http://.../uploads/file.png',
            'file' => /* objeto file crudo opcional */,
        ),
        'url' => '/portfolio/start-cms',
    ),
)
```

Valores EAV hoy son JSON (`{"title":"Start CMS"}`). El hidratador debe **aplanar** al valor útil del campo (string, bool, número, o imagen con `url`). Templates no parsean JSON.

Colección:

```php
(object) array(
    'name' => 'home_portfolio',  // form_name
    'slug' => 'home_portfolio',
    'description' => '...',
    'template' => 'portfolio',
    'items' => array( /* ítems */ ),
)
```

### 4.4 Plantillas Blade (fallback en `application/views/site/templates/collections/`)

Archivos:

| File | Uso |
|---|---|
| `default.blade.php` | Lista genérica: `title` + resto de `fields` escalares |
| `portfolio.blade.php` | Grid: imagen + título + enlace si existe `fields.url` o similar |
| `cards.blade.php` | Cards título + texto |
| `team.blade.php` | Foto + nombre + cargo si hay campos típicos |
| `faq.blade.php` | dt/dd o accordion simple (HTML semántico, sin JS admin) |

Markup **neutro** (clases `scms-collection`, `scms-collection--portfolio`, `scms-collection-item`). El tema puede sobrescribir el archivo entero. No copiar CSS del admin. No Materialize en el fallback (el sitio público no lo comparte). HTML + clases BEM-like `scms-*`.

Ejemplo mínimo `portfolio.blade.php`:

```blade
<section class="scms-collection scms-collection--portfolio">
  @foreach ($collection->items as $item)
    <article class="scms-collection-item">
      @if (!empty($item->fields['image']->url))
        <img src="{{ $item->fields['image']->url }}" alt="{{ $item->title }}">
      @endif
      <h3>{{ $item->title }}</h3>
    </article>
  @endforeach
</section>
```

PHP 7.4 / BladeOne: no `?->`. Usar `!empty()`.

### 4.5 Override de tema

`themes/` está en `.gitignore`. El fallback en `application/views/site/` **debe funcionar sin tema**. Documentar en este mismo archivo (y un comentario en el helper) la ruta de override:

`{theme}/views/site/templates/collections/{template}.blade.php`

API admin `GET /api/v1/models/templates` (espejo de `SiteformsController::templates_get`): lista archivos en fallback + tema, para el `<select>` del form de schema.

### 4.6 Cómo lo usa una página

El editor pega el snippet en el HTML de una Page o un Fragment. No hay bind automático página↔colección en v1.

---

## 5. Admin: flujos y pantallas

### 5.1 Rutas a añadir / corregir

En `application/config/routes.php` (además de las existentes):

```
admin/custommodels/items/(:num)  → CustomModelsController/items/$1
```

Método nuevo `items($custom_model_id)`: Blade lista de entradas **de ese tipo**.

Corregir: `$route['admin/custommodels/edit/(:num)']` hoy apunta a `editar` **inexistente**. Mapear a `editForm/$1`.

FAB y sidenav “new”: un solo destino `/admin/custommodels/new` (ya enrutado a `nuevo`). El FAB de la lista usa `nuevo/` — unificar a `new`.

No hace falta `/items` global.

### 5.2 Lista de tipos (`list.blade.php` + `CustomModelsLists.js`)

Dejar de clonar páginas:

- **No** `getPageImagePath` / imagen default de página.
- **No** reveal con category / subcategory / template de **página** / `page_type_name` / `model.path`.
- Card de tipo: nombre, descripción, slug, template, nº de campos, nº de ítems, status (activa / archivada).
- Acciones `more_vert`: Ver ítems, Añadir ítem, Editar schema, copiar snippet, Eliminar.
- Click en título → ítems del tipo (`items/{id}`), no `target=_blank` a path vacío.
- Empty state: icono + `lang('collections_empty')` + CTA a crear (`DESIGN.md`).
- Tabla: columnas reales (nombre, slug, ítems, status, opciones). Hoy status y fecha están cruzadas — alinear.
- FAB accent, no `btn-large red`.
- Loader: quitar `setTimeout(..., 1000/3000)` de init; usar `initPlugins()` del mixin en `$nextTick`.

API lista: cada tipo debe incluir `items_count` (count query, no hidratar todas las entradas). Añadir en `filter_results` o endpoint dedicado.

### 5.3 Schema form (`form.blade.php` + `CustomModelModule.js`)

- Nombre + slug (slug autogenerado al tipear nombre, editable) + descripción + **select template** (GET templates).
- Select “Title field” (Api IDs del schema) cuando ya hay campos; en alta puede quedar vacío y resolverse al primer title.
- Paleta de campos: copy `lang('collections_fields')` (“Fields” / “Campos”).
- Switch Enabled/Disabled → status 1/3.
- Tras save: toast `lang(...)` + acción “Añadir ítem” y “Ver ítems”.
- Validación cliente+API: nombre required, slug required `[a-z0-9_]+`, slug único, al menos un tab y un campo. Hoy `//@todo: validate data`.

Presets (si se implementan): chips “Portfolio / Team / FAQ / Cards” que rellenan `tabs[0].custom_model_fields` desde un mapa JS (mismos `formsElements` de `start.js`) y setean `template` + `slug` sugerido.

### 5.4 Lista de ítems por tipo (`content_list` reusada o vista nueva)

Nueva vista preferida: `admin/custommodels/items.blade.php` + JS que pida `GET /api/v1/models/form_data/{custom_model_id}` o `GET /api/v1/models/data?custom_model_id=`.

Hoy `form_data_get` con id filtra `status = 1` y `as_single_object` (solo fields, pierde ids). **Corregir el contrato:**

- `GET /api/v1/models/data?custom_model_id={id}` → entradas de ese tipo (todos los status ≠ 0), paginadas, con `title`, `featured`, `sort_order`, `data` aplanado, `custom_model`.
- No usar `as_single_object` para el admin list.

UI:

- Navbar: volver a colecciones, título = nombre del tipo.
- Card: miniatura si hay campo imagen; si no, placeholder genérico (no foto de página).
- Título = `content.title`.
- Acciones: editar, eliminar. Dropdown **único por `custom_model_content_id`** (hoy las cards reusan `dropdown` + `custom_model_id` y se pisan).
- FAB: añadir ítem de **este** tipo.
- Filtros: status chips publicado/borrador.
- Orden: mostrar `sort_order`; v1 puede ser input numérico al editar, no hace falta drag.

`/admin/custommodels/content` (lista global): si se deja, es búsqueda admin. No está en sidenav. Puede redirigir a `/admin/custommodels/`.

### 5.5 Form de ítem (`content_form` + `CustomModelContentModule.js`)

- Header: nombre de la colección, no “Modelos”.
- Campos `configurable: false` (ya).
- Switch Publicado / Borrador → status 1/2.
- Campos extra: destacado (checkbox), `sort_order` (número).
- Cancelar → `items/{custom_model_id}`, no siempre al listado de tipos.
- Save: un POST; toast en el idioma de la UI; permanecer en edit con id.

Bug a no reproducir: `saveData` asigna `response.data.custom_model_id` pero `data_post` hoy devuelve `true`. Devolver `{ custom_model_content_id, custom_model_id }`.

### 5.6 Vista huérfana

`data_list.blade.php` carga `DataFormModule.js` (formulario, no lista). No usarla. No enrutar. Se puede dejar el archivo; no invertir en ella.

---

## 6. API (`ModelsController`)

Mantener prefijo `/api/v1/models`.

| Método | Cambio |
|---|---|
| `GET /models` | Incluir `slug`, `template`, `title_field`, `items_count`, `snippet` |
| `GET /models/{id}` | Schema completo + esos campos |
| `POST /models` | Validar; save/update no destructivo; `system_logger` |
| `DELETE /models/{id}` | Soft delete tipo (status 0). No borrar físicamente tabs si el delete del modelo ya es soft |
| `GET /models/data` | Query `custom_model_id`; no N+1 |
| `POST /models/data` | Una fila content; devolver ids |
| `POST /models/data_set_status/{id}` | Solo 1 o 2 (o 0 si se usa para delete). No `0` como “inactive” |
| `GET /models/templates` | **Nuevo.** Lista de templates de colección |
| `GET /models/form_data/{id}` | Si se mantiene para el tema, devolver ítems publicados normalizados. El admin no depende de este shape viejo |

Auth: sigue `verify_request()`. El front público **no** llama esta API; usa el helper.

`DashboardController`: `forms_types` + `collections` alias. `content` puede seguir para el widget (máx 5 ítems, con `title`).

---

## 7. i18n

Añadir/renombrar **solo copy**. Archivos:

- `application/language/english/admin/admin_lang.php`
- `application/language/spanish/admin/admin_lang.php`

Claves nuevas (mínimo):

```
menu_collections
menu_all_items                    // solo si se deja el ítem global
tooltip_new_collection
collections_empty
collections_empty_cta
collections_snippet
collections_snippet_help
collections_slug
collections_slug_help
collections_template
collections_title_field
collections_add_item
collections_view_items
collections_items_count
collections_enabled
collections_disabled
collections_featured
collections_sort_order
dashboard_latest_collection_items
dashboard_collection_items_total
```

Reusar `custommodels_*` donde el texto ya sirva; actualizar los que dicen Form/Model. Toasts del JS: `lang()` inyectado desde Blade (patrón Site Forms `siteforms_i18n.blade.php`) — no strings sueltos en JS.

---

## 8. Permisos

No crear claves nuevas. Opcional en la migración: `UPDATE permisions SET label = '...' WHERE permision_name = 'CREATE_FORM_CUSTOM'` para que el panel de roles diga “Add collection” en lugar de “Add form custom”. El `module` puede seguir `form_custom`.

Checks en controller: los mismos `SELECT_FORM_CUSTOMS` etc.

---

## 9. SQL muerto y deuda que sí tocar si se pasa por el modelo

`CustomModelModel` tiene SQL que referencia tablas/columnas viejas (`form_custom_data`, `form_content`, `cm.id`). Si un método no se usa, no llamarlo. Si se usa, reescribir contra `custom_model*`. No dejar queries a tablas inexistentes en el camino de `get_collection`.

`filter_results` de content: N+1 (user + model + un query por field). v1 del helper y de `GET data?custom_model_id=` debe cargar data en lote.

---

## 10. Archivos previstos

| Área | Archivos |
|---|---|
| Migración | `application/database/migrations/005_collections.sql` + delta en `start.sql` |
| Helper | `application/helpers/general_helper.php` |
| Templates fallback | `application/views/site/templates/collections/{default,portfolio,cards,team,faq}.blade.php` |
| Modelos | `CustomModelModel.php`, `CustomModelContentModel.php` |
| API | `application/controllers/api/v1/ModelsController.php`, `DashboardController.php` |
| Admin PHP | `CustomModelsController.php`, `routes.php` |
| Blade admin | `list`, `form`, `content_form`, `content_list` o `items`, `sidenav`, `dashboard`, `create_contents_component`, perfil |
| JS | `CustomModelsLists.js`, `CustomModelModule.js`, `CustomModelContentList.js` (o nuevo ItemsList), `CustomModelContentModule.js`, `CreateContents.js`, `SearchPalette.js`, `CalendarList.js`, `DashboardModule.js` |
| Lang | EN + ES `admin_lang.php` |
| Design | No hace falta CSS nuevo si se reutilizan `.page-card` / empty states; templates públicos: CSS mínimo inline o clases `scms-*` sin SCSS del admin |

No editar `vendor/`, `public/vendors/`, `public/css/admin/*.min.css`. SCSS solo si el empty/snippet lo necesita (`resources/scss/admin/`).

Tras JS: `npm run build` si se toca `resources/js/start.js` (presets / `formsElements`). Componentes en `resources/components/` se sirven directo.

---

## 11. Criterios de aceptación

1. Menú dice Colecciones / Collections. Dashboard FAB primario = nueva página.
2. Crear colección “Portfolio” con slug `home_portfolio`, template `portfolio`, campos título/imagen/url.
3. Añadir 2 ítems publicados y 1 borrador. Lista `/items/{id}` muestra títulos; el borrador no sale en el sitio.
4. Una Page o Fragment con `{!! get_collection('home_portfolio') !!}` renderiza el grid en el tema (o fallback si no hay override).
5. Editar el placeholder de un campo **no** rompe los ítems existentes (mismos `custom_model_fields_id`).
6. Desactivar la colección (status 3): deja de listarse en admin `all()` y `get_collection` devuelve `''`.
7. Switch del tipo nunca escribe `status = 0`.
8. Copy de las pantallas tocadas pasa por `lang()`. Un idioma por pantalla.
9. PHP 7.4: nada de `?->` / `match`.
10. Permisos: un usuario sin `CREATE_FORM_CUSTOM` no crea tipos; con `CREATE_CONTENT_DATA` puede añadir ítems.

---

## 12. Orden de implementación sugerido

1. Migración + hidratar slug/template en modelo GET.
2. Helper + templates fallback + snippet en el form (aunque la lista admin aún sea fea). Probar con el seed `home_portfolio`.
3. `save_data_form` / `update_form` no destructivos + status 1/2/3.
4. Lista de ítems por tipo + arreglo dropdown ids.
5. Brand: sidenav, dashboard FAB, lang, listas de tipos (quitar campos de página).
6. Presets de schema (si queda tiempo).
7. Limpieza: Search, Calendar, perfil, toasts.

No invertir el orden: sin helper el resto no es producto.

---

## 13. Notas para el agente

- Un worktree, rama desde master, PR desde esa rama.
- No “corregir” typos históricos: `permisions`, `categorie`, `fielApiID`, `patern`, `bolean`.
- Verificar en browser: login `gerber` / `admin123`, crear colección, ítem, pegar snippet en una página, ver el front.
- Si el tema actual no existe en el worktree (`themes/` gitignore), el fallback `application/views/site/templates/collections/` es la prueba.
- Actualizar `docs/DESIGN.md` una línea: FAB dashboard = nueva página; módulo se llama Colecciones en UI.
- `graphify --update` no es obligatorio en este corte (no es un refactor de grafo).
