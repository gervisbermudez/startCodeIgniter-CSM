# Prompt — corte de bugs de vistas admin

Copiar el bloque de abajo a un **chat nuevo** (un chat = un worktree). No usar Cloud Agents. No editar `master`.

El inventario completo está en [ADMIN_VIEWS_AUDIT.md](ADMIN_VIEWS_AUDIT.md) (re-eval **2026-09-01** contra master). Este corte es **solo bugs P0 + P1 que siguen rotos**. No componentizar listas. No colapsar `pages_list`. No migrar forms a `formMixin` / `formComponents`.

La rama `fix/admin-views-p0` ya está mergeada con master. Hay un stash `wip: admin-views P0/P1 before merge master`: **no hacer `stash pop`**. Cherry-pick por archivo; login y lang a mano (§9 del audit).

---

## Texto para pegar al agente

```
Lee AGENTS.md, docs/DESIGN.md y docs/ADMIN_VIEWS_AUDIT.md (secciones 3, 5, 5b y 9).

Trabajá SOLO en el Git worktree local de esta feature (fix/admin-views-p0, ya nivelada con master). No Cloud Agents. No docker compose. No toques ci_php56 ni :8081. No edites master. PHP 7.4, sin sintaxis 8+. No “corrijas” permisions / categorie / albumes. No edites vendor/. public/vendors/vue/vue-router.js SÍ hay que añadir (punto 10) — no git add -u de public/vendors/ (el worktree a menudo muestra vendors borrados). SCSS en resources/scss/admin/, no public/css/admin/*.min.css. JS en resources/, no public/js/.

Objetivo: corregir los bugs P0 y P1 de docs/ADMIN_VIEWS_AUDIT.md §3 que SIGUEN rotos. Nada de extraer partials, unificar cards, ni reescribir formularios. No reimplementar §5b (product-copy / login i18n / fragment-card / page-intro).

Si hay stash de P0/P1: NO hagas stash pop. Cherry-pick archivos. login.blade.php y language/*/admin/*.php a mano.

## P0 (obligatorio)

1. menu_list.blade.php y categories_list.blade.php: el título de la card NO debe ser base_url(name). Apuntar a editar (admin/menus/editar/{id}, admin/categories/editar/{id}). Dejar “ver en sitio” del dropdown con path.

2. AlbumsLists.js y AlbumsItemsLists.js: file_front_path ya es absoluto. No concatenar BASEURL. Seguí el mixin getPageImagePath de resources/js/start.js. Default: BASEURL + "public/img/default.jpg" (BASEURL ya termina en /).

3. CustomModelItemsList.js: statusFilter con serverPagination debe ir en el query al API (status + custom_model_id), página 1 al cambiar chip. No filtrar solo la página actual en cliente. En ModelsController::data_get: whitelist status 0|1|2|3; si no viene, dejar status_in [1,2,3]. No quitar unfiltered=>true. No tocar form_data_get.

4. Tooltips :data-tooltip="lang('…')" en videos_list (published/draft; archived/deleted ya son PHP), categories_list (incl. FAB) y events/create_event: usar PHP lang() en Blade. window.ADMIN_LANG no tiene esas claves.

5. dashboard.blade.php: el switch pone html.dark-mode; el <style> usa body.dark-mode. Pintar con html.dark-mode y var(--st-*). Mover a SCSS SOLO lo que no esté ya en resources/scss/admin/dashboard.scss (KPI/charts ya están). No pegar el <style> entero. npm run build.

6. login.blade.php: SOLO el </a> extra (~línea 49). Labels/botón/marca YA son lang() + ADMIN_BRAND_NAME. No volver a tocarlos. No cambiar type="submit".

7. layouts/app.blade.php y layouts/login.blade.php: html lang según idioma CI (english→en, spanish→es), no "en" fijo. Helper html_lang() en general_helper.php está bien.

8. UserNewForm.js ~196 (y el error de save ~90): el callback recibe `error`, no `response`. mixins: [mixins] + this.toastError(error). Evitar ReferenceError.

9. PagesLists.js getcontentText: si page.content es null/undefined, devolver "" — no llamar .replace.

10. data_table_component.blade.php carga vue-router 2.0.0 desde unpkg. Sacá esa URL. Commiteá public/vendors/vue/vue-router.js (Vue Router 2.x) y un partial admin.components.vue_router SOLO en SiteFormSubmitList, LoggerDataComponent, ApiLoggerDataComponent, PermissionsDataComponent, UserTrackingLoggerDataComponent. No borres vue.js. Sin ese archivo trackeado las pantallas quedan en blanco.

## P1 (mismo PR si cabe; no ampliar)

11. navbar.blade.php: Settings / View site / Logout con lang('menu_settings'), lang('view_site'), lang('logout') — las tres claves YA existen. No uses view_in_site. alt de avatares con el username.

13. menu_list: copy visible por lang() (headers, empty, confirm, FAB). Claves nuevas tooltip_new_menu, menus_empty, menus_confirm_delete (EN+ES). Sin extraer componentes. No toques page-intro (ya está, PR #38).

15. file_explorer: ocultar el <a href="#!"> Recents del nav (~73, sin @click). No tocar el heading "Recently Accessed Files". No implementar Recents.

16. VideosNewForm: toast de error con this.toast('toast_error'), no español hardcodeado. mixins: [mixins]. No reescribir el form.

19. Solo EventNewForm fetch().catch que espera response.error_message. toastError / toast_error. No recorrer el resto de forms.

## Fuera de alcance (no hacer)

- Colapsar pages_list / extraer status_filters, create_fab, list_empty_state, entity-list-card.
- Rehacer i18n de chips/empty/FAB de pages_list (ya hecho en master).
- Un more_vert por card, formMixin en Page/Videos/User, validar MenuNewForm/SiteFormNewForm.
- Tocar page-intro (ya está en las listas admin, PR #38).
- Borrar content_list.blade.php o data_list.blade.php.
- Temas públicos, Graphify, docker compose, composer install en el host.
- Reabrir ítems ya fijos (ADMIN_VIEWS_AUDIT.md §5 y §5b): archive modal, tabs #basic/#seo, type=submit, login labels, fragment-card, page-intro.
- git add -u de public/vendors, uploads, trash, application/cache.

## Cómo trabajar

- Chat en el worktree de fix/admin-views-p0. El inventario vive en docs/admin-views-audit (rama aparte, ya nivelada con master).
- Preview UI: regla worktree-preview (docker run, puerto 8082–8099, misma DB, cookie SESS_COOKIE_NAME distinta). Nunca :8081.
- Verificar en browser (o curl si no hay browser): login; lista menús/categorías (click título); lista álbumes (img src); ítems de una colección + chip Borrador (debe pedir status al API); videos/categorías tooltips; dashboard dark mode; file explorer sin Recents; una pantalla con data-table (eventos o logs) sin petición a unpkg.
- Commit solo si te lo pido. Mensaje en inglés, 1–2 frases, el porqué.
```
