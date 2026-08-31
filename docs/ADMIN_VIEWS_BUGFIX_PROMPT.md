# Prompt — corte de bugs de vistas admin

Copiar el bloque de abajo a un **chat nuevo** (un chat = un worktree). No usar Cloud Agents. No editar `master`.

El inventario completo está en [ADMIN_VIEWS_AUDIT.md](ADMIN_VIEWS_AUDIT.md). Este corte es **solo bugs P0 + P1 listados**. No componentizar listas. No colapsar `pages_list`. No migrar forms a `formMixin` / `formComponents`.

---

## Texto para pegar al agente

```
Lee AGENTS.md, docs/DESIGN.md y docs/ADMIN_VIEWS_AUDIT.md (secciones 3 y 5).

Trabajá SOLO en un Git worktree local. No Cloud Agents. No docker compose. No toques ci_php56 ni :8081. No edites master. PHP 7.4, sin sintaxis 8+. No “corrijas” permisions / categorie / albumes. No edites vendor/, public/vendors/ salvo para vendorar vue-router 2.x si hace falta (punto 10). SCSS en resources/scss/admin/, no public/css/admin/*.min.css. JS en resources/, no public/js/.

Objetivo: corregir los bugs P0 y P1 de docs/ADMIN_VIEWS_AUDIT.md §3. Nada de extraer partials, unificar cards, ni reescribir formularios.

## P0 (obligatorio)

1. menu_list.blade.php y categories_list.blade.php: el título de la card NO debe ser base_url(name). Apuntar a editar (admin/menus/editar/{id}, admin/categories/editar/{id}). Dejar “ver en sitio” del dropdown con path.

2. AlbumsLists.js y AlbumsItemsLists.js: file_front_path ya es absoluto. No concatenar BASEURL. Seguí el mixin getPageImagePath de resources/js/start.js.

3. CustomModelItemsList.js: statusFilter con serverPagination debe ir en el query al API (status + custom_model_id), página 1 al cambiar chip. No filtrar solo la página actual en cliente.

4. Tooltips :data-tooltip="lang('…')" en videos_list, categories_list (incl. FAB) y events/create_event: usar PHP lang() en Blade. window.ADMIN_LANG no tiene esas claves; el lang() JS muestra el key name.

5. dashboard.blade.php: el switch pone html.dark-mode; el CSS usa body.dark-mode. Pintar con html.dark-mode y var(--st-*). Sacar el <style> a SCSS del admin.

6. login.blade.php: hay un </a> extra (~línea 49). Labels Username/Password y botón Login por lang('username'|'password'|'login'). No cambiar type="submit".

7. layouts/app.blade.php y layouts/login.blade.php: html lang según idioma CI (english→en, spanish→es), no "en" fijo.

8. UserNewForm.js ~196: el error callback recibe `error`, no `response`. Toast con mixin (toast_error). Evitar ReferenceError.

9. PagesLists.js getcontentText: si page.content es null/undefined, devolver "" — no llamar .replace.

10. data_table_component.blade.php carga vue-router 2.0.0 desde unpkg. Sacá esa URL. Vendor local 2.x o script solo en vistas que usan new VueRouter / <router-view> (SiteFormSubmitList, LoggerDataComponent, ApiLoggerDataComponent, PermissionsDataComponent, UserTrackingLoggerDataComponent). No romper esas pantallas.

## P1 (mismo PR si cabe; no ampliar)

11. navbar.blade.php: Settings / View site / Logout con lang() (menu_settings, view_in_site o clave equivalente, logout). alt de avatares con el username, no alt="".

13. menu_list: copy visible por lang() (headers, empty, confirm, FAB). Sin extraer componentes.

15. file_explorer: ocultar Recents (href="#!" muerto). No implementar Recents.

16. VideosNewForm: toast de error con lang/toast_error, no español hardcodeado. No reescribir el form.

19. fetch .catch que espera response.error_message: el catch es Error. toastError / toast_error.

## Fuera de alcance (no hacer)

- Colapsar pages_list / extraer status_filters, create_fab, list_empty_state, entity-list-card.
- Un more_vert por card, formMixin en Page/Videos/User, validar MenuNewForm/SiteFormNewForm.
- Borrar content_list.blade.php o data_list.blade.php.
- Temas públicos, Graphify, docker compose, composer install en el host.
- Reabrir ítems ya fijos (ADMIN_VIEWS_AUDIT.md §5): archive modal, tabs #basic/#seo, type=submit del login.

## Cómo trabajar

- Chat en el worktree. Rama tipo fix/admin-views-p0 (no docs/admin-views-audit si esa rama es solo el doc; branched off master o de docs/admin-views-audit está bien).
- Preview UI: regla worktree-preview (docker run, puerto 8082–8099, misma DB, cookie SESS_COOKIE_NAME distinta). Nunca :8081.
- Verificar en browser (o curl si no hay browser): login; lista menús/categorías (click título); lista álbumes (img src); ítems de una colección + chip Borrador; videos/categorías tooltips; dashboard dark mode; file explorer sin Recents; una pantalla con data-table (eventos o logs) sin petición a unpkg.
- Commit solo si te lo pido. Mensaje en inglés, 1–2 frases, el porqué.
```
