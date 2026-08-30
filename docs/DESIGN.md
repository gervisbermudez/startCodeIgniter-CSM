# Guía de diseño — admin startCodeIgniter CSM

Fuente de verdad para UI del panel. El sitio público vive en `themes/` y **no** comparte estos tokens (el tema es del cliente). Esta guía cubre solo el **admin**.

Stack visual: Materialize CSS + Vue 2 + Blade + SCSS en `resources/scss/admin/`. No editar `public/css/admin/*.min.css`.

---

## Principios

1. **Una cromática.** Chrome slate, acento coral, interacción teal. Nada de un cuarto color “porque Materialize lo trae”.
2. **Un idioma por pantalla.** Todo el copy pasa por `lang()`. No mezclar español e inglés en el mismo flujo.
3. **Una acción primaria.** Un botón filled por vista. El resto: flat, icono con tooltip, o menú `more_vert`.
4. **Estilos en SCSS.** Cero `<style>` en Blade y cero `style=""` en Vue salvo excepciones puntuales (posición de un FAB ya existente).
5. **Tokens, no hex sueltos.** Si hace falta un color, se añade a `resources/scss/admin/components/palette.scss` y se usa la clase o la variable.
6. **Estados vacíos con salida.** Nunca un `<h4>` solo. Icono + texto + CTA.
7. **Icono solo = tooltip + `aria-label`.** Materialize `tooltipped` no sustituye accesibilidad.
8. **No “corregir” typos históricos** en URLs, tablas o claves (`permisions`, `categorie`, `albumes`). En **copy visible** sí se puede escribir bien (“categoría”, “álbumes”).

---

## Tokens

Definidos en `resources/scss/admin/components/_tokens.scss` (SCSS compile-time) y emitidos como custom properties en `resources/scss/admin/components/palette.scss`. Materialize (`_variables.scss`) se alinea a estos valores; no al revés.

Usar `var(--st-*)` en SCSS del admin. Dark mode es `html.dark-mode` redefiniendo las mismas vars — no un segundo set de hex ni `* { color }`.

| Token SCSS | CSS var | Light | Dark | Uso |
|---|---|---|---|---|
| `$color4` / `$color-chrome` | `--st-chrome` | `#646b7f` | `#404648` | Navbar, rail, barra `::before` de formularios |
| `$color3` / `$color-accent` | `--st-accent` | `#fb9678` | igual | FAB, ítem de menú actual, CTA de login |
| `$teal` / `$color-interactive` | `--st-interactive` | `#26A69A` | igual | Focus, switch, chip activo, enlace, tabs |
| `$color5` / `$color-canvas` | `--st-canvas` | `#edf0f5` | `#1c1f20` | Fondos de widgets / hover muted |
| `$page-bg` | `--st-page` | `#f9f9f9` | `#141617` | `body`, `.main` |
| `$surface` | `--st-surface` | `#fff` | `#121414` | Cards, sidenav, formularios |
| `$text-primary` | `--st-text` | `#444` | `#e0e0e0` | Cuerpo |
| `$gray-text` | `--st-text-secondary` | `#5D5D5D` | `#aeadac` | Texto secundario |
| `$color-danger` | `--st-danger` | `#F44336` | igual | Borrar, error |
| `$color-success` | `--st-success` | `#4CAF50` | igual | Publicado, KPI positivo |
| `$color-warning` | `--st-warning` | `#ff9800` | igual | Archivado |
| `$color-neutral` | `--st-neutral` | `#757575` | igual | Borrador |
| `$color-trash` | `--st-trash` | `#424242` | igual | Papelera |
| `$navbar-height-admin` | `--st-navbar-height` | `50px` | igual | Navbar |
| `$sidemenuWidthExpanded` | `--st-sidenav-expanded` | `210px` | igual | Sidenav |
| `$radius-md` | `--st-radius-md` | `8px` | igual | Cards nuevas |

Clases utilitarias: `.st-teal`, `.st-pink`, `.st-chrome`, `.st-accent`, `.st-surface`, `.status-published` / `draft` / `archived` / `deleted`. Preferirlas a hex en Blade.

**No usar** (legado a retirar cuando se toque el archivo):

- `$primary-color` Materialize red (`#ee6e73`) — el admin ya lo apunta a `$color-accent`.
- Light blue `#03a9f4` / `#039be5` en tabs, blockquotes y `card-action`.
- Gradientes en badges de estado (`pages_list.blade.php`).
- `btn-large red` en FABs: usar `$color3` / `var(--st-accent)`.
- `page-new.scss` es un dump de Bootstrap; no es la paleta del admin.

### Tipografía

| Rol | Familia | Peso | Tamaño |
|---|---|---|---|
| Marca (logo) | Coda | 400 | 22px navbar / 30px login |
| UI | Roboto | 400 | 14–15px (`html` en `_typography.scss`) |
| Títulos de página | Roboto | 400 | `.page-header` 20px — **no** usar `h1`–`h3` de Materialize (4.2rem / 3.56rem / 2.92rem son de marketing, no de admin) |
| KPI / números | Roboto | 600–800 | dashboard only |
| Iconos | Material Icons | 24px | sidenav 1.6rem |

Un icon set: **Material Icons**. File explorer mezcla Font Awesome; en pantallas nuevas no añadir FA.

### Espaciado y layout

| Pieza | Valor |
|---|---|
| Navbar | 50px (no los 64px de Materialize) |
| Sidenav expandido | 210px (`$sidemenuWidthExpanded`) |
| Sidenav colapsado | 70px |
| Contenido desktop | `padding-top: 50px` + padding-left = ancho sidenav |
| Container | `width: 90%` — listas de cards usan `.pages { max-width: 96% }` |
| Gutter Materialize | 1.5rem |
| Radio de card | **12px** en listas de contenido (`.page-card`) · 4px dashboard welcome |
| FAB | `bottom: 45px; right: 24px` |

Breakpoints a respetar (los de `mixins.scss` + el corte real del sidenav):

- sidenav overlay: `max-width: 992px`
- phone: `max-width: 576px` (`phone-only`)
- No introducir un tercer set. Materialize 600/993/1201 se queda para la grilla `s/m/l`.

### Elevación

Materialize `z-depth-1` está bien para cards y dropdowns. No añadir `box-shadow` custom ni `translateY` en hover de cards nuevas (el lift de `pages_list` es ruido). Hover = borde o fondo, no movimiento.

### Motion

- Sidenav collapse: `0.3s ease`
- Vue fade existente: `0.5s opacity`
- No nuevas animaciones de hover en cards
- `scroll-behavior: smooth` ya está en `html`

---

## Color semántico de contenido

Status del CMS (`0` eliminado · `1` publicado · `2` borrador · `3` archivado):

| Status | Chip | Icono |
|---|---|---|
| 1 Publicado | success `#4CAF50` | `check_circle` |
| 2 Borrador | grey `#757575` | `edit` |
| 3 Archivado | warning `#ff9800` | `archive` |
| 0 Papelera | dark `#424242` | `delete_outline` |

Visibilidad: público `public` + interactive teal; privado `lock` + grey. **Chips planos**, no degradados.

---

## Dark mode

El switch escribe `html.dark-mode` + `localStorage`. **No** uses `body.dark-mode` (el dashboard lo hace y no se aplica).

Reglas:

- Pintar **superficies** con `var(--st-surface)` / `var(--st-page)` / `var(--st-text)`. Las vars se redefinen en `html.dark-mode` (`palette.scss`).
- No `* { color }`. Contraste mínimo 4.5:1 en texto de cuerpo (`--st-text` `#e0e0e0` sobre `--st-surface` `#121414`).
- Acento y CTA **siguen** `--st-accent` / `--st-interactive`.
- Prefer `prefers-color-scheme` como default si no hay localStorage (hoy no existe).

---

## Patrones de pantalla

### Shell

`admin.layouts.app` = navbar fija + sidenav + `.main`.

- Navbar: búsqueda global (GET `/admin/search`), dark mode, notificaciones, menú de usuario.
- Sidenav: contrato único de estados (no mezclar con Materialize `.active` ni `$primary-color`):

| Estado | Clase | Visual |
|---|---|---|
| Hover | — | fondo `rgba(0,0,0,.05)`, sin lift |
| Sección abierta (accordion) | `li.active` | sin relleno extra; no es “estoy aquí” |
| Ruta actual | `li.current` | barra `$color3` de 5px + fondo muted + icono `$color3` |
| Hoja actual | `li.current` en el hijo | la misma barra/fondo; `aria-current="page"` en el `<a>` |

- Sección actual abre el accordion (`current active` en el `li` padre). La hoja se marca con `isNavItemActive()` (exacto o `path*` para edit). Settings vive en el rail principal (`configNavCurrent()`): Overview, General, Appearance, SEO, Integrations, System, Updates, Backups, Import, Export, Logs. No hay un segundo sidebar interno de configuración.
- Colapsado (`body.sidenav-open`, el nombre está invertido): solo iconos. **Click** en hamburguesa o en un header de sección expande. **No** hover-peek. Con rail colapsado, tooltip a la derecha + `aria-label` en el toggle.
- Labels siempre en `<span>`. Enlaces del menú: `waves-effect`.
- Iconos: Files `folder`, Events `event`, Menus `view_list`, Models `view_module`, Analytics `assessment`. `account_tree` no está en el woff2 de Material Icons del admin (es Material Symbols); no usar `menu` (conflicto con la hamburguesa).

Dashboard en sidenav solo aparece `show-on-medium-and-down`. En desktop el logo es el home. Mantener ese contrato o añadir Dashboard también en desktop; no dejarlo a medias.

### Lista (CRUD)

Referencia vertical: páginas. **No** copiar `pages_list.blade.php` tal cual: está triplicada (Results / Pages / Blogs) y el menú “Archive” del card abre el sitio público.

Estructura canónica:

1. Loader: `<preloader />` (o skeleton si la vista ya lo tiene).
2. `nav.page-navbar` con búsqueda (`lang('search_placeholder')`), toggle vista, refresh. El icono de toggle refleja el modo actual (`view_module` ↔ `view_list`).
3. Filtros de status como `.status-chip` (botones o `<button>`, no `<div>`).
4. Grid de cards **o** tabla, un solo `v-for` sobre la lista filtrada.
5. Vacío: título + una línea + botón a crear.
6. Búsqueda sin resultados: “Ningún resultado para X” + limpiar filtro. Distinto del vacío total.
7. FAB `btn-floating btn-large` accent + tooltip `lang(...)`.
8. `confirm-modal` para delete/archive.

Acciones por ítem: FAB coral `more_vert` en la imagen (esquina superior derecha), criterio de las cards de páginas. Estilos compartidos en `resources/scss/admin/components/_entity-cards.scss` (`.page-card`, `.custom-badge`). Pills de status/visibilidad: `admin.components.entity_card_badges`. Grid: wrapper `.pages`.

### Formulario de edición

Referencia: `pages/new.blade.php` (a refinar, no a clonar ciegas).

- Fondo: `.form` blanco sobre la franja chrome (`.main::before` 120px).
- Tabs con ids semánticos (`#basic`, `#seo`), no `#test1`.
- Columna principal (contenido) + aside (publicación, visibilidad, taxonomía).
- **Barra de acciones sticky** al final del viewport: Cancelar (flat) + primaria (Guardar borrador / Publicar según `status`). Hoy el submit queda bajo el fold.
- Un `id` por campo. El form de página duplica `id="title"`.
- Preview y “ver en sitio” son secundarios (flat o texto), no otro `.btn` filled.
- Autoguardado no existe; el copy de la primaria debe decir el efecto (“Publicar”, “Guardar borrador”), no “Submit”.

### Login

`admin.layouts.login`. Marca Coda + logo SVG. CTA `$color3`. Wallpaper solo desktop (`hide-on-med-and-down`).

- Labels y botón también por `lang()`.
- El submit debe ser `type="submit"` (hoy es `type="button"` + click: Enter es frágil).
- `html lang` = idioma de sesión, no `"en"` fijo.

### Dashboard

Grid 70/30. KPI + welcome counts + charts + rail (creator, drafts, timeline).

- El FAB del dashboard no debería ser “nuevo custom model” como acción por defecto. Preferir “nueva página” o un speed-dial sin primaria engañosa.
- El widget “Type here…” (creator) es un atajo opaco. Si se mantiene: placeholder que nombre el destino (“Borrador de página”).
- Copy de KPIs por `lang()`, no “Unique Visitors” hardcodeado.

### Explorador de archivos

Chrome tipo drive: árbol + breadcrumbs + grid.

- Un icon set (Material). Recents no puede ser un `<a href="#!">` muerto: o se implementa o se oculta.
- “Add File” / “My Drive” por `lang()`.

---

## Feedback

| Evento | Patrón |
|---|---|
| Éxito / error de API | `M.toast({ html: lang('...') })` |
| Confirmación destructiva | `<confirm-modal>` (mixin en `start.js`) |
| Carga de lista | preloader o skeleton, `v-cloak` para evitar flash |
| Validación de campo | clases Materialize `.valid` / `.invalid` + mensaje bajo el input |
| Progreso de save | disable del botón + label “Guardando…” |

Toasts: un idioma. Hoy conviven `"Ocurrió un error inesperado"`, `"An unexpected error occurred"`, `"Done!"`, `"Datos guardados!"`. Extraer a `common_lang.php`.

---

## Accesibilidad (mínimo)

- `html lang` correcto.
- Icon-only: `aria-label` (navbar search, dark mode, notificaciones, hamburguesa, more_vert).
- `img` de avatar: `alt` con el nombre, no `alt=""`.
- Chips y filtros: focus visible y activables con teclado.
- Contraste: texto `#444` sobre `#f9f9f9` OK; `#c2c2c2` sobre dark surface no.
- No depender del hover del sidenav colapsado para descubrir subítems (hoy `.sidenav:hover` abre labels). En colapsado, click = expandir, no peek.

---

## i18n

Claves en `application/language/{english,spanish}/admin/`. Helper `lang()`.

Pantallas con más copy huérfano (prioridad):

- `shared/navbar.blade.php` — Search, Settings, Logout, No notifications
- `pages/pages_list.blade.php` — casi todo el UI
- `dashboard.blade.php` — KPIs, Top Pages, Create
- `files/file_explorer.blade.php` — My Drive, labels
- Toasts en `resources/components/*.js`

---

## Qué no hacer en PRs de UI

- No pegar `<style>` en `@section('header')`.
- No introducir Bootstrap ni otro icon font.
- No usar `h1`/`h2` Materialize a tamaño display dentro del admin.
- No clonar la lista de páginas entera para un recurso nuevo: extraer el patrón corto de arriba.
- No “arreglar” `permisions` / `categorie` en rutas o API.
- No activar `M.AutoInit()` global (rompe Vue). Plugins en `$nextTick` + `initPlugins()` del mixin.

---

## Inventario de deuda (auditoría)

Prioridad para trabajo de UX, no un backlog de features. Detalle y archivos en el canvas de auditoría.

**P0 — roto o engañoso**

1. “Archive” en cards de páginas (y categorías) abre la URL pública, no archiva.
2. ~~`<li>` duplicado en sidenav de fragments~~ (corregido).
3. Dark mode del dashboard (`body.dark-mode`) no coincide con el switch (`html.dark-mode`).
4. Tabs del editor de página: ambos con `.active`; ids `#test1` / `#test2`.
5. `id="title"` duplicado en el form de página.

**P1 — duele cada día**

6. Tres paletas en paralelo (Materialize red/teal, `palette.scss`, hex `#03a9f4`) — tokens CSS en curso; tabs/links del admin ya usan `--st-interactive`.
7. Dark mode con `* { color }` — contraste y marca rotos. Corregido: vars en `html.dark-mode`, sin `* { color }`.
8. ES/EN mezclados (toasts, navbar, listas, “Guardar Borrador” junto a “Preview”).
9. Editor de página: save al final, sin sticky, sin feedback de dirty state.
10. Lista de páginas triplicada e inconsistente (restore/archive según el bloque).
11. Estados vacíos pobres; Recents del file explorer muerto.
12. ~~Hover-expand del sidenav colapsado; iconos semánticamente incorrectos~~ (corregido: click expande; iconos según tabla del Shell).

**P2 — pulido**

13. FABs rojos vs accent coral.
14. Dos `more_vert` por card de página (FAB + activator); es el patrón visual de listado.
15. ~~Estilos inline de cards en `pages_list`~~ (pasados a `_entity-cards.scss`). Quedan inline en dashboard.
16. Login `type="button"`; `html lang="en"` fijo; `alt` vacíos.
17. Filtros de status como `<div>`, no botones.
18. Widget creator y FAB del dashboard con destino poco obvio.

Al implementar, seguir esta guía. Si un patrón nuevo se vuelve recurrente, documentarlo aquí — no en un `<style>` de una vista.
