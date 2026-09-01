# Fragments — Corte B: token, preview, chips, permisos

El Corte A ya publica de verdad (`status = 1`), guarda TinyMCE, pinta `{{fragment(nombre)}}` y bustea cache. Este corte es el contrato de **uso** en el admin: copiar el token, ver el HTML, filtrar, insertar imagen, y que el módulo no sea visible para cualquier logueado.

**Rama:** `feat/fragments-module`  
**Worktree:** `/home/gervis/.cursor/worktrees/startCodeIgniter-CSM/fragments-module`  
**Checkout principal:** `/home/gervis/personal/startCodeIgniter-CSM` (`ci_php56`, `:8081` — no tocarlo)

PHP 7.4 + CodeIgniter 3.1. Sin PHP 8+. No Cloud Agents. No `docker compose`. No edites `master`.

**Leer este archivo entero. No ampliar alcance.**

---

## 1. Objetivo (job mínimo)

1. En el form se ve `{{fragment(nombre)}}` y un botón **Copiar**. Es el contrato con páginas.
2. **Preview admin** del HTML (modal). No hay URL pública `/fragments/{name}`; Preview no es “ver en el sitio”.
3. Lista: chips de **status** (todos / publicado / borrador) y de **type** (metadato editorial). `fragment()` sigue resolviendo solo por **nombre** + `status = 1`.
4. TinyMCE puede **insertar imagen** del file explorer (mismo patrón que Events).
5. Al **renombrar**, toast de que los tokens viejos se rompen. No hay tabla de aliases.
6. Permisos `SELECT_FRAGMENT` / `CREATE_FRAGMENT` / `UPDATE_FRAGMENT` / `DELETE_FRAGMENT`: seed + `$routes_permisions` + API 403 + sidenav `@if`.

## 2. Fuera de alcance

- Columna `date_publish` / datepicker / programar salida. Los fragmentos no tienen destino público; la página que los embebe ya tiene su propio publish. No fingir la columna.
- Aliases al renombrar (`{{fragment(viejo)}}` → nombre nuevo). Cache bust de ambos nombres ya está en Corte A.
- Rutas públicas `/fragments/{name}`, EAV `fragmentos_data`, reescribir TinyMCE → Trumbowyg.
- `docker compose`, `composer install` en el host, Cloud Agents, editar `master`.
- “Corregir” `fragmentos` / `permisions`. Editar `vendor/`, `public/vendors/`, `graphify-out/`, CSS minificado.

## 3. Decisiones de producto

| Tema | Decisión |
|---|---|
| `type` | Se queda. Label editorial + filtro de lista. El helper **no** lo usa. |
| Rename | Se rompen los tokens viejos. Aviso al guardar. Sin aliases. |
| Preview | Modal admin con el HTML. No es una página del sitio. |
| `date_publish` | Diferido. No hay job de programar un fragmento suelto. |

## 4. Cómo verificar

1. Form: el token cambia con el nombre; Copiar deja `{{fragment(foo)}}` en el clipboard; toast copied.
2. TinyMCE: botón imagen abre el file explorer; inserta `<img>` en el HTML guardado.
3. Preview (lista y form): se ve el HTML, no una 404 ni “view in site”.
4. Chips: Published / Draft filtran la API (`?status=1|2`). Type `?type=contenido` igual. All = `status_in` 1,2 (sin `status = 0`).
5. Rename `foo` → `bar`: toast de warning; `{{fragment(foo)}}` deja de pintar; `{{fragment(bar)}}` pinta.
6. Sin `SELECT_FRAGMENT`: sidenav Fragments oculto; `/admin/fragments` error de permisos; `GET /api/v1/fragments` 403. Con `SELECT_PAGES` pero sin fragment: tab Fragment en New Page oculto.
7. No `ALTER` de `fragmentos`. Migración = INSERT en `permisions` / `usergroup_permisions`.
