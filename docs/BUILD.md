# Build System (Start CMS 3.0)

Vite compiles **SCSS only**. Admin JavaScript stays unbundled (Vue 2 globals, no `import`/`export`). Gulp is not part of this project.

`npm run copy-js` concatenates a few **runtime** files so the footer does not load jQuery, Materialize, mixins, chrome, and form fields as separate requests. Page islands (`PagesLists.js`, `DashboardModule.js`, …) stay as globals in `resources/components/`.

## Commands

```bash
npm run build      # copy+concat JS, then compile SCSS
npm run watch      # copy+watch JS and recompile SCSS
npm run watch:css  # SCSS only
npm run watch:js   # copy resources/js and rebuild concat bundles on change
npm run copy-js    # copy resources/js + concat admin-runtime / chrome / form-fields / dashboard-widgets
npm run dev        # Vite dev server (SCSS)
```

## What the browser loads

| Asset | Source of truth | Served from |
|---|---|---|
| Admin runtime (jQuery → Materialize → `start.js`) | `resources/js/` | `public/js/admin-runtime.js` |
| Navbar chrome (notifications + search palette) | `resources/components/` | `public/js/admin-chrome.js` |
| Collection form fields | `resources/components/formComponents/` | `public/js/form-fields.js` |
| Dashboard widgets | `resources/components/widget/` | `public/js/dashboard-widgets.js` |
| Page Vue islands | `resources/components/` | Same path. Edit and reload; **do not** copy to `public/js/components/`. |
| SCSS | `resources/scss/admin/` | `public/css/admin/*.min.css` via Vite. Do not edit those outputs. |

`public/js/components/` is not used by the admin. Do not treat it as source.

After changing `resources/js/` or `resources/components/formComponents/` (or chrome/widgets used in a bundle), run `npm run copy-js` (or `watch`).

## One load path for page scripts

Each admin view loads its Vue file in **one** place: `@section('footer_includes')` in the Blade template.

Do **not** also inject the same file from PHP. A view→file map in the controller used to 404 (`PageForm.js`) and double-mount Vue when Blade already had the real script.

Vendors (TinyMCE, fileinput, Chart.js, vue-router) stay in that same Blade section, **before** the Vue file that needs them. Vue templates (`@include('admin.components.data_table_component')`) belong in `content` or in `footer_includes` **before** the matching `.js`.

`resources/js/validateForm.js` is loaded from `resources/` in Blade. `npm run copy-js` also copies it to `public/js/` for anything that still points there.

Shared footer always loads: Vue, `public/js/admin-runtime.js`, `public/js/admin-chrome.js`. Login footer skips chrome. The `$footer_includes` PHP loop in `footer.blade.php` is unused for admin Vue.

## Why JS is not Vite-bundled

Globals (`Vue`, `$`, `M`, `mixins`, `BASEURL`) and per-page `new Vue({ el: "#root" })` islands. Bundling those islands would require `import` and break Blade templates. Concatenating the shared runtime does not change that contract.
