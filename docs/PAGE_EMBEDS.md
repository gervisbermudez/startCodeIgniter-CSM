# Embeds de productos en New Page

> **Estado (Start CMS 3.0):** shipped en `master` (`feat: Expand page helper tokens on the public site from New Page`). Spec del corte. Código vivo: `expand_page_embeds()` en `application/helpers/general_helper.php`. Whitelist actual también incluye `get_collection` (más allá del spec original de 6 helpers). No reimplementar.

**Stack:** PHP 7.4, CodeIgniter 3.1, BladeOne, Vue 2 global, Materialize. Sin PHP 8+ (`match`, `?->`, union types, named arguments).
**Guías:** `AGENTS.md`, `docs/DESIGN.md`, `.cursor/rules/`.

Leer este archivo para el contrato. No ampliar alcance sin un corte nuevo.

---

## 1. Objetivo

En **New Page / Edit Page** el editor inserta productos de sitio (formulario, álbum, etc.) en el HTML de `page.content`. El front público los **renderiza** al mostrar la página.

No es page builder. No es Gutenberg. No se usa `json_content`. El contenido sigue siendo HTML de Trumbowyg más tokens `{{helper(nombre)}}`.

---

## 2. Cómo está hoy

New Page guarda HTML en `page.content` (`resources/components/PageNewForm.js`, textarea `#editor`). El tema pinta `{!! $page->content !!}`.

Helpers públicos ya existentes (`application/helpers/general_helper.php`):

- `render_form($name)` → `site.templates.forms.{template}`
- `fragment($name)` → HTML del fragmento (cache)
- `render_menu($name)` → `site.templates.menu.{template}`

Álbum, video y evento tienen CRUD/API y **no** helper de render.

Expander proto en `Base_Controller::get_page_info()` (`application/core/MY_Controller.php`, ~líneas 399–414):

- Extrae **un** `{{...}}` con `get_string_between`.
- Si `is_callable($fn)` ejecuta `call_user_func_array` (cualquier función PHP).
- Reemplaza ese único token.

Por eso un formulario escrito a mano como `{{render_form(contacto)}}` ya puede pintarse. Fallos: un solo token, inseguro, sin UI.

Insertar imagen ya existe (`uploadimage` + `trumbowygInstance.range.insertNode`). Reutilizar ese patrón de inserción en el cursor.

---

## 3. Alcance: solo productos de sitio

Criterio: lo que un visitante vería en el front. No herramientas internas del CMS.

**Sí (picker + expander):**

| Producto | Token | Helper | Estado |
|---|---|---|---|
| Formulario | `{{render_form(contacto)}}` | `render_form` | shipped |
| Fragmento | `{{fragment(aviso-legal)}}` | `fragment` | shipped |
| Menú | `{{render_menu(main)}}` | `render_menu` | shipped |
| Álbum | `{{render_album(verano)}}` | `render_album` | shipped |
| Video | `{{render_video(trailer)}}` | `render_video` | shipped |
| Evento | `{{render_event(lanzamiento)}}` | `render_event` | shipped |
| Colección | `{{get_collection(home_portfolio)}}` | `get_collection` | shipped (3.0, extra al spec v1) |

Whitelist en código: esos nombres. Lookup por **nombre** publicado (`status = 1`). Video: columna de nombre en tabla `video` es `nam` (typo histórico; no “corregir”).

**No:**

- Usuarios, grupos, permisos
- Config, analytics, cache, logger, notificaciones
- Archivos (ya hay insertar imagen)
- Páginas anidadas
- Categorías (taxonomía)
- Custom models / colecciones — el spec v1 las dejó fuera; en 3.0 `get_collection` **sí** está en la whitelist de `expand_page_embeds()`.
- Inbox de formularios, calendario admin, notes internas

---

## 4. Expander (seguridad + N tokens)

Sustituir el bloque `is_callable` en `get_page_info()` por un helper `expand_page_embeds($html): string`.

Comportamiento:

1. `preg_match_all` de tokens `{{nombre(args)}}` (varios por página).
2. Nombre debe estar en whitelist. Si no: dejar el token **sin ejecutar** (así `{{phpinfo()}}` no corre y no desaparece como si se hubiera “renderizado”).
3. Args: un identificador/nombre; quitar comillas simples/dobles y espacios. No evaluar PHP.
4. Llamar solo al helper whitelistado. Recurso inexistente, borrado o no publicado (`status != 1`): reemplazar por cadena vacía.
5. Aplicar en `get_page_info` para que preview (`PageController::preview`) y 404 configurado también expandan.

No `eval`, no compilar Blade sobre `page.content`, no `is_callable` genérico.

---

## 5. Helpers nuevos + plantillas fallback

Añadir en `application/helpers/general_helper.php` (mismo estilo que `render_form` / `render_menu`):

- `render_album($name)` — `AlbumModel` por `name`, `status = 1`, con `items`. Vista `site.templates.albums.default`.
- `render_video($name)` — `VideoModel` por `nam`, `status = 1`. Iframe YouTube con `youtube_id`. Vista `site.templates.videos.default`.
- `render_event($name)` — `EventModel` por `name`, `status = 1`. Card: nombre, fecha, dirección, extracto. Vista `site.templates.events.default`.

Plantillas **fallback** en el core (el tema puede sobrescribir la misma ruta relativa):

- `application/views/site/templates/albums/default.blade.php`
- `application/views/site/templates/videos/default.blade.php`
- `application/views/site/templates/events/default.blade.php`

Si `getThemePath()` tiene el archivo, usarlo; si no, `APPPATH` (como `render_menu`).

No editar `themes/` del cliente. No `docker compose`.

---

## 6. Admin: picker en New Page

Archivos:

- `application/views/admin/pages/new.blade.php` — botón + modal
- `resources/components/PageNewForm.js` — lógica
- SCSS en `resources/scss/admin/` (no `public/css/admin/*.min.css`)
- Strings `lang()` EN + ES en `application/language/*/admin/`

UI (`docs/DESIGN.md`):

- Un botón filled **Insertar contenido** bajo el editor (no plugin vendor de Trumbowyg).
- Modal Materialize con pestañas: formulario, fragmento, menú, álbum, video, evento.
- Listas GET (auth JWT/sesión ya la tiene el admin):
  - `api/v1/siteforms`
  - `api/v1/fragments`
  - `api/v1/menus`
  - `api/v1/albumes`
  - `api/v1/videos`
  - `api/v1/events`
- Filtrar publicados. Mostrar nombre. Click inserta el token en el cursor (`insertNode` o `trumbowyg('html')` como las imágenes).
- V1: token de texto plano `{{render_form(nombre)}}`. No badge HTML (Trumbowyg lo puede romper).
- Si el usuario no tiene `SELECT_*` del módulo, ocultar esa pestaña o no pedir la lista.
- Insertar no crea el producto; solo referencia.

Copy: un idioma por pantalla, todo por `lang()`. Icono solo = tooltip + `aria-label`.

Tras JS: si el panel carga `resources/components/` directo, no hace falta copiar a `public/js/` para PageNewForm. SCSS sí: `npm run build` / `npm run watch`.

---

## 7. Qué no hacer

- No PHP 8+
- No Cloud Agents, no `/in-cloud`, no `/best-of-n`
- No `docker compose up/down/restart` en este worktree
- No `composer install` en el host
- No “corregir” typos: `permisions`, `albumes`, `fragmentos`, `nam`, `categorie`
- No reactivar Gutenberg / `PageViewComponent` / `json_content`
- No tocar `vendor/`, `public/vendors/`, `graphify-out/`
- No fusionar con el worktree de colecciones (`feat/collections`)

---

## 8. Orden de implementación

1. `expand_page_embeds` + quitar `is_callable` en `get_page_info`.
2. `render_album`, `render_video`, `render_event` + Blade fallback.
3. Modal + botón en New Page (6 pestañas).
4. i18n EN/ES + SCSS mínimo.
5. Verificar en el navegador (abajo).

---

## 9. Verificación

App: `http://localhost:8081` (stack del checkout principal, contenedor `ci_php56`). Login `gerber` / `admin123`.

1. New Page: insertar un formulario publicado y un álbum (o video) en la misma página. Guardar.
2. Abrir la URL pública de la página y el preview admin (`admin/pages/preview?page_id=`). Ambos pintan el form y la galería/video, no el token crudo.
3. Token de un nombre inexistente: no se ve el `{{...}}`.
4. Escribir `{{phpinfo()}}` en el contenido: **no** se ejecuta (el texto puede quedar visible o sanitizado, pero no el output de phpinfo).
5. No verificar analytics, users ni config.

---

## 10. Entrega

Commit y PR **desde `feat/page-embeds`**. No editar `master` ni otros worktrees.
