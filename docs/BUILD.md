# Build System (Start CMS 3.0)

Vite compiles **SCSS only**. Admin JavaScript stays unbundled (Vue 2 globals, no `import`/`export`). Gulp is not part of this project.

## Commands

```bash
npm run build      # copy resources/js → public/js, then compile SCSS
npm run watch      # copy+watch resources/js and recompile SCSS
npm run watch:css  # SCSS only
npm run watch:js   # copy resources/js → public/js on change
npm run copy-js    # one-shot copy of resources/js
npm run dev        # Vite dev server (SCSS)
```

## What the browser loads

| Asset | Source of truth | Served from |
|---|---|---|
| Mixins / `lang()` helpers | `resources/js/start.js` | `public/js/start.js` (footer). Copy with `npm run build` or `watch`. |
| Page Vue islands | `resources/components/` (and `formComponents/`, `widget/`) | Same path. Edit and reload; **do not** copy to `public/js/components/`. |
| SCSS | `resources/scss/admin/` | `public/css/admin/*.min.css` via Vite. Do not edit those outputs. |

`public/js/components/` is not used by the admin. Do not treat it as source.

## One load path for page scripts

Each admin view loads its Vue file in **one** place: `@section('footer_includes')` in the Blade template.

Do **not** also inject the same file from PHP. A view→file map in the controller used to 404 (`PageForm.js`) and double-mount Vue when Blade already had the real script.

Vendors (TinyMCE, fileinput, Chart.js) stay in that same Blade section, **before** the Vue file that needs them. Vue templates (`@include('admin.components.data_table_component')`) belong in `content` or in `footer_includes` **before** the matching `.js`.

`resources/js/validateForm.js` is loaded from `resources/` in Blade. `npm run copy-js` also copies it to `public/js/` for anything that still points there.

Shared footer always loads: Vue, Materialize, jQuery, `public/js/start.js`, `NotificationsComponent.js`, `SearchPalette.js`. The `$footer_includes` PHP loop in `footer.blade.php` is unused for admin Vue.

## Why JS is not Vite-bundled

Globals (`Vue`, `$`, `M`, `mixins`, `BASEURL`) and per-page `new Vue({ el: "#root" })` islands. Bundling would require `import` and break Blade templates.
