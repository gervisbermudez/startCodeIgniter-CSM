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

Definidos en `resources/scss/admin/components/palette.scss`. Materialize (`_variables.scss`) se alinea a estos valores; no al revés.

| Token | Valor | Uso |
|---|---|---|
| `$color4` / chrome | `#646b7f` | Navbar, rail del sidenav, barra `::before` de formularios |
| `$color3` / accent | `#fb9678` | FAB, ítem de menú actual (barra 5px), CTA de login, indicadores |
| `$teal` / interactive | `#26A69A` | Focus, switch, chip activo, enlace, success suave de formularios |
| `$color5` / canvas | `#edf0f5` | Fondos de widgets / dashboard |
| Page background | `#f9f9f9` | `body`, `.main` |
| Surface | `#fff` | Cards, sidenav, formularios |
| `$gray-text` | `#5D5D5D` | Texto secundario |
| Texto primario | `#444` | Cuerpo |
| Danger | `#F44336` | Borrar, error, validación |
| Success | `#4CAF50` | Publicado, KPI positivo |
| Warning | `#ff9800` | Archivado |
| Neutral / draft | `#757575` | Borrador |

**No usar** (legado a retirar cuando se toque el archivo):

- `$primary-color` Materialize red (`#ee6e73`) — no es la marca.
- Light blue `#03a9f4` / `#039be5` en tabs, blockquotes y `card-action`.
- Gradientes en badges de estado (`pages_list.blade.php`).
- `btn-large red` en FABs: usar `$color3`.

Clases utilitarias ya existentes: `.st-teal`, `.st-pink`, `.text-st-white`, `.text-st-gray`. Preferirlas a hex en Blade.

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
| Radio de card | 8px (listas nuevas) / 4px (dashboard welcome) |
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

- Pintar **superficies** (`.card`, `.sidenav`, `.main`, `.collection-item`), no `* { color }`.
- Texto: `#e0e0e0` primario, `#aeadac` secundario.
- Acento y CTA **siguen** `$color3` / `$teal`.
- Contraste mínimo 4.5:1 en texto de cuerpo. El `* { color: #c2c2c2 }` actual **no** cumple sobre fondos `#121414` para texto pequeño — no copiarlo.
- Prefer `prefers-color-scheme` como default si no hay localStorage (hoy no existe).

---

## Patrones de pantalla

### Shell

`admin.layouts.app` = navbar fija + sidenav + `.main`.

- Navbar: búsqueda global (GET `/admin/search`), dark mode, notificaciones, menú de usuario.
- Sidenav: secciones con `collapsible`; ítem activo = `.current` + barra `$color3` de 5px.
- Iconos de menú (corregir cuando se toque el archivo):

| Sección | Icono correcto | Evitar |
|---|---|---|
| Files | `folder` | `markunread_mailbox` |
| Events | `event` | `assistant` (ya lo usan siteforms) |
| Menus | `account_tree` | `menu` (conflicto con hamburguesa) |
| Models | `view_module` | `assessment` (analytics) |
| Analytics | `assessment` | — |

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

Acciones por ítem: un solo `more_vert`. No FAB + activator de `card-reveal` a la vez (páginas tiene los dos).

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
2. `<li>` duplicado en sidenav de fragments (HTML inválido).
3. Dark mode del dashboard (`body.dark-mode`) no coincide con el switch (`html.dark-mode`).
4. Tabs del editor de página: ambos con `.active`; ids `#test1` / `#test2`.
5. `id="title"` duplicado en el form de página.

**P1 — duele cada día**

6. Tres paletas en paralelo (Materialize red/teal, `palette.scss`, hex `#03a9f4`).
7. Dark mode con `* { color }` — contraste y marca rotos.
8. ES/EN mezclados (toasts, navbar, listas, “Guardar Borrador” junto a “Preview”).
9. Editor de página: save al final, sin sticky, sin feedback de dirty state.
10. Lista de páginas triplicada e inconsistente (restore/archive según el bloque).
11. Estados vacíos pobres; Recents del file explorer muerto.
12. Hover-expand del sidenav colapsado; iconos semánticamente incorrectos.

**P2 — pulido**

13. FABs rojos vs accent coral.
14. Dos `more_vert` por card de página.
15. Estilos inline en Blade (páginas, dashboard).
16. Login `type="button"`; `html lang="en"` fijo; `alt` vacíos.
17. Filtros de status como `<div>`, no botones.
18. Widget creator y FAB del dashboard con destino poco obvio.

Al implementar, seguir esta guía. Si un patrón nuevo se vuelve recurrente, documentarlo aquí — no en un `<style>` de una vista.
