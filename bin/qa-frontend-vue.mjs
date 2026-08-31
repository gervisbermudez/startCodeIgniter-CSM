#!/usr/bin/env node
/**
 * Contract checks for the Vue 2 admin P0/P1/P2 audit.
 * No browser, no extra deps: asserts source still matches the audit plan.
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");

function read(rel) {
  return fs.readFileSync(path.join(root, rel), "utf8");
}

function exists(rel) {
  return fs.existsSync(path.join(root, rel));
}

const results = [];

function check(id, ok, detail) {
  results.push({ id: id, ok: !!ok, detail: detail || "" });
}

function src(rel) {
  if (!exists(rel)) {
    check("missing:" + rel, false, "file not found");
    return "";
  }
  return read(rel);
}

const albumItems = src("resources/components/AlbumsItemsLists.js");
const albumList = src("resources/components/AlbumsLists.js");
const albumBlade = src("application/views/admin/gallery/albums_items.blade.php");
check(
  "p0.2 albums items global",
  /var AlbumsItemsLists\s*=/.test(albumItems) && /var AlbumsLists\s*=/.test(albumList),
  "AlbumsItemsLists does not collide with AlbumsLists"
);
check(
  "p0.2 albums items delete",
  albumItems.includes("delete_album_item") && albumItems.includes("album_item_id"),
  "delete hits albumes/delete_album_item/{album_item_id}"
);
check(
  "p0.2 albums items blade",
  albumBlade.includes('v-for="(item, index)') &&
    !albumBlade.includes("admin/pages/editar") &&
    albumBlade.includes("view_in_site"),
  "Blade iterates items, not albums, and does not edit via admin/pages"
);

const statusOffenders = [];
const formFiles = fs
  .readdirSync(path.join(root, "resources/components"))
  .filter(function (f) {
    return f.endsWith(".js");
  });
formFiles.forEach(function (f) {
  const text = read("resources/components/" + f);
  if (/status:\s*this\.status\s*\?\s*1\s*:\s*0/.test(text)) {
    statusOffenders.push(f + " status");
  }
  if (/form_status:\s*this\.form_status\s*\?\s*1\s*:\s*0/.test(text)) {
    statusOffenders.push(f + " form_status");
  }
});
check(
  "p0.3 status 1|2",
  statusOffenders.length === 0,
  statusOffenders.length
    ? "still writes 0: " + statusOffenders.join(", ")
    : "unpublished saves use 2, never 0"
);

const events = src("resources/components/EventsList.js");
check(
  "p0.4 events api",
  events.includes('url: BASEURL + "api/v1/events/"') &&
    events.includes("type: \"DELETE\"") &&
    !/console\.log/.test(events),
  "EventsList delete/archive hit api/v1/events, not console.log"
);

const pageListBlade = src("application/views/admin/pages/pages_list.blade.php");
check(
  "p0.5 archive vs view in site",
  pageListBlade.includes("lang('archive')") &&
    pageListBlade.includes("lang('view_in_site')") &&
    pageListBlade.includes("setTempPage") &&
    pageListBlade.includes("base_url(page.path)"),
  "Pages cards: Archive opens modal, public path is View in site"
);

const myController = src("application/core/MY_Controller.php");
const pageNewBlade = src("application/views/admin/pages/new.blade.php");
check(
  "p0.6 no script map",
  !myController.includes("getAutoFooterIncludes") &&
    !pageNewBlade.includes("PageForm.js") &&
    pageNewBlade.includes("PageNewForm.js"),
  "Blade loads PageNewForm.js; PHP map that 404'd PageForm.js is gone"
);

const newForms = formFiles.filter(function (f) {
  return /NewForm\.js$/.test(f);
});
const copiedSelf = newForms.filter(function (f) {
  if (f === "UserNewForm.js") {
    return false;
  }
  return /let self = UserNewForm/.test(read("resources/components/" + f));
});
check(
  "p0.7 this not UserNewForm",
  copiedSelf.length === 0 &&
    /let self = UserNewForm/.test(src("resources/components/UserNewForm.js")),
  "only UserNewForm keeps let self = UserNewForm"
);
const loginJs = src("resources/components/loginForm.js");
check(
  "p0.7 ajax error arg",
  /error:\s*function\s*\(\s*response\s*\)/.test(loginJs) &&
    loginJs.includes("console.error(response)"),
  "loginForm logs the xhr/response argument"
);

const pageNew = src("resources/components/PageNewForm.js");
const btnEnable = pageNew.match(/btnEnable:\s*function\s*\(\)\s*\{[\s\S]*?\n    \}/);
check(
  "p0.8 PageNewForm validateForm",
  pageNew.includes("let errors = true") && !/let error = true/.test(pageNew),
  "validateForm uses errors, not a stray error global"
);
check(
  "p0.8 PageNewForm btnEnable",
  btnEnable && !btnEnable[0].includes("autoSave()"),
  "btnEnable has no autoSave() side effect"
);

const loginBlade = src("application/views/admin/login.blade.php");
check(
  "p0.9 login enter",
  loginBlade.includes('@submit.prevent="login"') && loginBlade.includes('type="submit"'),
  "login form submits on Enter"
);

const copyJs = src("bin/copy-js.sh");
check(
  "p1 copy-js js only",
  copyJs.includes("resources/js/*.js") &&
    copyJs.includes("public/js/") &&
    !/cp .*resources\/components/.test(copyJs),
  "copy-js copies resources/js, not components"
);

const startJs = src("resources/js/start.js");
const publicStart = src("public/js/start.js");
check(
  "p1 start.js copied",
  publicStart.includes("var listMixin") && publicStart.includes("var formMixin"),
  "public/js/start.js has the shared mixins the footer loads"
);
check(
  "p2 listMixin",
  /var listMixin\s*=/.test(startJs) &&
    startJs.includes("fetchList:") &&
    startJs.includes("this.$nextTick") &&
    /initPlugins:\s*function/.test(startJs),
  "listMixin fetches lists and inits Materialize on $nextTick"
);

const listInitTimeout = [];
[
  "CategoriesLists.js",
  "FragmentsLists.js",
  "AlbumsLists.js",
  "AlbumsItemsLists.js",
  "MenuLists.js",
  "VideosLists.js",
  "PagesLists.js",
  "CustomModelsLists.js",
  "UserComponent.js",
].forEach(function (f) {
  const text = src("resources/components/" + f);
  if (/updated:\s*function/.test(text) && /initPlugins/.test(text.match(/updated:[\s\S]{0,200}/) || [""][0])) {
    listInitTimeout.push(f + " updated");
  }
  if (/initPlugins[\s\S]{0,500}setTimeout\([^)]*(1000|2000|3000)/.test(text)) {
    listInitTimeout.push(f + " setTimeout");
  }
});
check(
  "p2 no list init timers",
  listInitTimeout.length === 0,
  listInitTimeout.length ? listInitTimeout.join(", ") : "lists do not re-init Materialize on a 1–3s timer or updated"
);

[
  "CategoryNewForm.js",
  "FragmentNewForm.js",
  "AlbumNewForm.js",
  "MenuNewForm.js",
  "EventNewForm.js",
  "ConfigNewForm.js",
  "SiteFormNewForm.js",
].forEach(function (f) {
  const text = src("resources/components/" + f);
  check("p2 formMixin " + f, text.includes("formMixin"), f + " uses formMixin");
});
check(
  "p2 PageNewForm stays special",
  /mixins:\s*\[mixins\]/.test(pageNew) && !pageNew.includes("formMixin"),
  "PageNewForm is not on formMixin"
);

const customModel = src("resources/components/CustomModelModule.js");
check(
  "p2 no AutoInit",
  !customModel.includes("M.AutoInit()"),
  "CustomModelModule does not call M.AutoInit()"
);

let titleRegs = 0;
formFiles.concat(["formComponents/formFieldTitle.js"]).forEach(function (f) {
  const rel = f.indexOf("/") === 0 ? f.slice(1) : "resources/components/" + f;
  const text = exists(rel) ? read(rel) : src("resources/components/" + f);
  const matches = text.match(/Vue\.component\(\s*["']formFieldTitle["']/g);
  if (matches) {
    titleRegs += matches.length;
  }
});
check(
  "p2 one formFieldTitle",
  titleRegs === 1,
  "Vue.component formFieldTitle registered " + titleRegs + " time(s)"
);

const enLang = src("application/language/english/admin/common_lang.php");
const esLang = src("application/language/spanish/admin/common_lang.php");
check(
  "p2 toast i18n",
  enLang.includes("$lang['toast_saved']") &&
    esLang.includes("$lang['toast_saved']") &&
    enLang.includes("$lang['view_in_site']") &&
    enLang.includes("$lang['config_export_desc']"),
  "toast keys plus master Settings export copy both present after merge"
);

const footer = src("application/views/admin/shared/footer.blade.php");
check(
  "p2 ADMIN_LANG",
  footer.includes("window.ADMIN_LANG") && footer.includes("public/js/start.js"),
  "footer exposes lang() toasts and loads public/js/start.js"
);

const passed = results.filter(function (r) {
  return r.ok;
}).length;
const failed = results.filter(function (r) {
  return !r.ok;
}).length;

results.forEach(function (r) {
  const mark = r.ok ? "PASS" : "FAIL";
  console.log(mark + "  " + r.id + " — " + r.detail);
});
console.log("");
console.log(passed + " passed, " + failed + " failed, " + results.length + " checks");
if (failed) {
  process.exit(1);
}
