function patchMaterializePlugins() {
  if (!window.M) {
    return;
  }

  function dropdownTargetEl(trigger) {
    if (!trigger || trigger.nodeType !== 1) {
      return null;
    }
    var id =
      typeof M.getIdFromTrigger === "function"
        ? M.getIdFromTrigger(trigger)
        : trigger.getAttribute("data-target");
    if (!id) {
      return null;
    }
    return document.getElementById(id);
  }

  function wrapPluginInit(Plugin, isReady) {
    if (!Plugin || typeof Plugin.init !== "function" || Plugin.init._stPatched) {
      return;
    }
    var origInit = Plugin.init.bind(Plugin);
    var wrapped = function (els, options) {
      if (els instanceof Element) {
        if (!isReady(els)) {
          return null;
        }
        return origInit(els, options);
      }
      if (!els || typeof els.length !== "number") {
        return origInit(els, options);
      }
      var instances = [];
      var i;
      for (i = 0; i < els.length; i++) {
        if (isReady(els[i])) {
          instances.push(origInit(els[i], options));
        }
      }
      return instances;
    };
    wrapped._stPatched = true;
    Plugin.init = wrapped;
  }

  wrapPluginInit(M.Dropdown, function (el) {
    return !!dropdownTargetEl(el);
  });
  wrapPluginInit(M.FormSelect, function (el) {
    return !!(
      el &&
      el.tagName === "SELECT" &&
      el.parentNode &&
      document.body &&
      document.body.contains(el)
    );
  });
}

patchMaterializePlugins();

jQuery(document).ready(function ($) {
  patchMaterializePlugins();
  // Disable AutoInit to prevent errors with dynamic Vue content
  // Each Vue component will handle its own Materialize initialization
  // M.AutoInit();

  function isRailCollapsed() {
    return $("body").hasClass("sidenav-open");
  }

  function syncSidenavToggleLabel() {
    var btn = document.querySelector("a.sidenav-trigger-lg");
    if (!btn) {
      return;
    }
    var collapsed = isRailCollapsed();
    var expandLabel = btn.getAttribute("data-label-expand") || "Expand menu";
    var collapseLabel = btn.getAttribute("data-label-collapse") || "Collapse menu";
    btn.setAttribute("aria-expanded", collapsed ? "false" : "true");
    btn.setAttribute("aria-label", collapsed ? expandLabel : collapseLabel);
  }

  function destroySidenavTooltips() {
    var tips = document.querySelectorAll("#slide-out .sidemenu-tooltip");
    var i;
    for (i = 0; i < tips.length; i++) {
      var instance = M.Tooltip.getInstance(tips[i]);
      if (instance) {
        instance.destroy();
      }
      tips[i].classList.remove("tooltipped", "sidemenu-tooltip");
      tips[i].removeAttribute("data-tooltip");
      tips[i].removeAttribute("data-position");
    }
  }

  function syncSidenavTooltips() {
    destroySidenavTooltips();
    if (!isRailCollapsed()) {
      return;
    }
    var items = document.querySelectorAll(
      "#slide-out > li > a, #slide-out > li > .collapsible-header"
    );
    var i;
    for (i = 0; i < items.length; i++) {
      var el = items[i];
      var label = el.querySelector("span");
      var text = label
        ? label.textContent.replace(/^\s+|\s+$/g, "")
        : el.textContent.replace(/^\s+|\s+$/g, "");
      if (!text) {
        continue;
      }
      el.setAttribute("data-tooltip", text);
      el.setAttribute("data-position", "right");
      el.classList.add("tooltipped", "sidemenu-tooltip");
    }
    M.Tooltip.init(document.querySelectorAll("#slide-out .sidemenu-tooltip"), {
      position: "right",
    });
  }

  function setRailCollapsed(collapsed) {
    $("body").toggleClass("sidenav-open", !!collapsed);
    $("#slide-out").removeAttr("style");
    if (collapsed) {
      localStorage.setItem("sidenav-open", "sidenav-open");
    } else {
      localStorage.removeItem("sidenav-open");
    }
    syncSidenavToggleLabel();
    syncSidenavTooltips();
  }

  function openSidenavSection(headerEl) {
    var slideOut = document.getElementById("slide-out");
    if (!slideOut || !headerEl) {
      return;
    }
    var li = headerEl.parentNode;
    var instance = M.Collapsible.getInstance(slideOut);
    if (!instance || !li) {
      return;
    }
    var items = slideOut.children;
    var index = -1;
    var i;
    for (i = 0; i < items.length; i++) {
      if (items[i] === li) {
        index = i;
        break;
      }
    }
    if (index >= 0) {
      instance.open(index);
    }
  }

  // Initialize only static elements present in the layout
  var sidenavElems = document.querySelectorAll(".sidenav");
  if (sidenavElems.length > 0) {
    M.Sidenav.init(sidenavElems, {});
    $("#slide-out").removeAttr("style");
  }

  var navbarDropdowns = document.querySelectorAll(
    ".main-navbar .dropdown-trigger"
  );
  if (navbarDropdowns.length > 0 && M.Dropdown) {
    M.Dropdown.init(navbarDropdowns, { constrainWidth: false });
  }

  var collapsibleElems = document.querySelectorAll(".collapsible:not(#slide-out)");
  if (collapsibleElems.length > 0) {
    M.Collapsible.init(collapsibleElems, {});
  }
  var slideOutEl = document.getElementById("slide-out");
  if (slideOutEl && slideOutEl.classList.contains("collapsible")) {
    M.Collapsible.init(slideOutEl, {});
  }

  $("a.sidenav-trigger-lg").click(function (e) {
    e.preventDefault();
    setRailCollapsed(!isRailCollapsed());
  });

  // Collapsed rail: click a section to expand, then open that accordion.
  // Capture + stop so Materialize does not toggle (which would close the current section).
  if (slideOutEl) {
    slideOutEl.addEventListener(
      "click",
      function (e) {
        if (!isRailCollapsed()) {
          return;
        }
        var header = e.target.closest
          ? e.target.closest(".collapsible-header")
          : null;
        if (!header || header.parentNode.parentNode !== slideOutEl) {
          return;
        }
        e.preventDefault();
        e.stopPropagation();
        setRailCollapsed(false);
        openSidenavSection(header);
      },
      true
    );
  }

  $("#darkmode-switch").change(function (e) {
    e.preventDefault();
    $("html").toggleClass("dark-mode");
    if ($(this).is(":checked")) {
      localStorage.setItem("dark-mode", "dark-mode");
    } else {
      localStorage.removeItem("dark-mode");
    }
  });

  $("#darkmode-switch").prop("checked", false);

  if (localStorage.getItem("dark-mode")) {
    $("html").toggleClass("dark-mode");
    $("#darkmode-switch").prop("checked", true);
  }

  if (localStorage.getItem("sidenav-open")) {
    setRailCollapsed(true);
  } else {
    syncSidenavToggleLabel();
  }
});

function normalizePathname(pathname) {
  return (pathname || "").replace(/\/+$/, "") || "/";
}

function queryParam(search, name) {
  var query = (search || "").replace(/^\?/, "");
  if (!query) {
    return "";
  }
  var parts = query.split("&");
  var i;
  for (i = 0; i < parts.length; i++) {
    var pair = parts[i].split("=");
    if (decodeURIComponent(pair[0] || "") === name) {
      return decodeURIComponent(pair[1] || "");
    }
  }
  return "";
}

function markSidenavConfigLeaf(section, defaultSection) {
  var here = normalizePathname(window.location.pathname);
  var wanted = section || defaultSection || "";
  var links = document.querySelectorAll("#slide-out .collapsible-body a");
  var i;
  for (i = 0; i < links.length; i++) {
    var a = links[i];
    var li = a.parentNode;
    if (!li || !li.classList) {
      continue;
    }
    if (normalizePathname(a.pathname || "") !== here) {
      continue;
    }
    var linkSection = queryParam(a.search, "section") || defaultSection || "";
    if (linkSection === wanted) {
      li.classList.add("current");
      a.setAttribute("aria-current", "page");
    } else {
      li.classList.remove("current");
      a.removeAttribute("aria-current");
    }
  }
}

function bindSamePageSidenavSections(onSection) {
  var root = document.getElementById("slide-out");
  if (!root || root.getAttribute("data-config-section-bound") === "1") {
    return;
  }
  root.setAttribute("data-config-section-bound", "1");
  root.addEventListener("click", function (e) {
    var a = e.target.closest ? e.target.closest("a") : null;
    if (!a || !a.href || a.getAttribute("href") === "#") {
      return;
    }
    if (normalizePathname(a.pathname || "") !== normalizePathname(window.location.pathname)) {
      return;
    }
    e.preventDefault();
    var section = queryParam(a.search, "section");
    if (typeof onSection === "function") {
      onSection(section, a);
    }
  });
}

// Función que resuelve un camino en un objeto
function resolve(obj, path) {
  // Separamos el camino en un array
  path = path.split(".");
  // Establecemos el objeto actual como el objeto inicial
  var current = obj;
  // Mientras que el camino tenga elementos
  while (path.length) {
    // Si el objeto actual no es de tipo "object" (por ejemplo, es una cadena), retornamos indefinido
    if (typeof current !== "object") return undefined;
    // Establecemos el objeto actual como el siguiente objeto del camino
    current = current[path.shift()];
  }
  // Retornamos el objeto actual al final del camino
  return current;
}

function getFuncName() {
  return getFuncName.caller.name;
}

if (window.M && M.Dropdown && M.Dropdown.defaults) {
  M.Dropdown.defaults.constrainWidth = false;
}

var mixins = {
  data() {
    return {
      debug: DEBUGMODE, // Variable que indica si el modo debug está activado o no
      orderDataConf: {
        // Configuración para ordenamiento de datos
        strPropertyName: null, // Propiedad por la cual se ordena (por defecto es null)
        sort_as: "asc", // Ordenamiento ascendente por defecto
      },
      toDeleteItem: {}, // Variable para guardar el ítem a eliminar
      showPagination: false,
      paginator: {},
      paginatorLinks: [],
      currentPage: 1,
      serverPagination: false,
      tableView: false,
      _searchTimer: null,
    };
  },
  filters: {
    capitalize: function (value) {
      // Filtro para capitalizar una cadena de texto
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  computed: {
    viewToggleIcon: function () {
      return this.tableView ? "view_list" : "view_module";
    },
  },
  methods: {
    resetFilter: function () {
      this.filter = "";
    },
    isFuturePublish: function (item) {
      if (!item || !item.date_publish) {
        return false;
      }
      var parsed = Date.parse(String(item.date_publish).replace(" ", "T"));
      return !isNaN(parsed) && parsed > Date.now();
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      if (typeof this.initPlugins === "function") {
        this.initPlugins();
      }
    },
    refreshList: function () {
      if (typeof this.reloadList === "function") {
        this.reloadList(this.currentPage || 1);
        return;
      }
      if (typeof this.getData === "function") {
        this.getData(this.currentPage || 1);
      }
    },
    onNavbarSearch: function () {
      if (this.client_search || (!this.pagination && !this.serverPagination)) {
        return;
      }
      clearTimeout(this._searchTimer);
      if (typeof this.reloadList === "function") {
        this.reloadList(1);
        return;
      }
      if (typeof this.getData === "function") {
        this.getData(1);
      }
    },
    searchInObject: function (object, strSearchTerm, depth) {
      if (object === null || typeof object === "undefined") {
        return false;
      }
      if (typeof depth === "undefined") {
        depth = 0;
      }
      if (depth > 3) {
        return false;
      }
      var valueType = typeof object;
      if (valueType === "string" || valueType === "number" || valueType === "boolean") {
        return String(object).toLowerCase().indexOf(strSearchTerm) !== -1;
      }
      if (valueType !== "object") {
        return false;
      }
      if (typeof User === "function" && object instanceof User) {
        return false;
      }
      var keys = Object.keys(object);
      for (var i = 0; i < keys.length; i++) {
        var key = keys[i];
        if (key === "user" || typeof object[key] === "function") {
          continue;
        }
        if (this.searchInObject(object[key], strSearchTerm, depth + 1)) {
          return true;
        }
      }
      return false;
    },
    getFullFileName(file) {
      // Función para obtener el nombre completo de un archivo
      return file.file_name + "." + file.file_type;
    },
    getFullFilePath(file) {
      // Función para obtener la ruta completa de un archivo
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    getSortData(strPropertyName) {
      // Función para obtener los datos de ordenamiento
      if (this.orderDataConf.strPropertyName == strPropertyName) {
        return "sort_desc";
      }
      if (this.orderDataConf.strPropertyName == "-" + strPropertyName) {
        return "sort_asc";
      }
      return "both";
    },
    sortData(strPropertyName, array) {
      // Función para ordenar los datos
      strPropertyName =
        this.orderDataConf.strPropertyName == null
          ? strPropertyName
          : this.orderDataConf.strPropertyName == strPropertyName
            ? "-" + strPropertyName
            : strPropertyName;
      let sorted = array.sort(this.dynamicSort(strPropertyName));
      this.orderDataConf.strPropertyName = strPropertyName;
      array = sorted;
    },
    dynamicSort(property) {
      // Función para ordenamiento dinámico
      var sortOrder = 1;
      if (property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
      }
      return function (a, b) {
        /* next line works with strings and numbers,
         * and you may want to customize it to your needs
         */
        var result =
          a[property] < b[property] ? -1 : a[property] > b[property] ? 1 : 0;
        return result * sortOrder;
      };
    },
    base_url: function (path) {
      if (!path) {
        return BASEURL;
      }
      path = String(path).replace(/^\//, "");
      return BASEURL + path;
    },
    getcontentText: function (html, length) {
      length = length || 120;
      if (!html) {
        return "";
      }
      if (typeof html === "object") {
        html =
          html.description ||
          html.content ||
          html.title ||
          "";
      }
      html = String(html);
      var span = document.createElement("span");
      span.innerHTML = html;
      var text = span.textContent || span.innerText || "";
      if (text.length <= length) {
        return text;
      }
      return text.substring(0, length) + "...";
    },
    makeid: function (length) {
      // Función para generar un ID aleatorio
      var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      var charactersLength = characters.length;
      let result = "";
      for (var i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },
    string_to_slug: function (str) {
      if (str.length == 0) return "";

      str = str.replace(/^\s+|\s+$/g, ""); // trim
      str = str.toLowerCase();

      // remove accents, swap ñ for n, etc
      var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
      var to = "aaaaeeeeiiiioooouuuunc------";
      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
      }

      str = str
        .replace(/[^a-z0-9 -]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

      return str;
    },
    getFormattedDate: function (
      date,
      prefomattedDate = false,
      hideYear = false
    ) {
      const MONTH_NAMES = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];
      const day = date.getDate();
      const month = MONTH_NAMES[date.getMonth()];
      const year = date.getFullYear();
      const hours = date.getHours();
      let minutes = date.getMinutes();

      if (minutes < 10) {
        // Adding leading zero to minutes
        minutes = `0${minutes}`;
      }

      if (prefomattedDate) {
        // Today at 10:20
        // Yesterday at 10:20
        return `${prefomattedDate} at ${hours}:${minutes}`;
      }

      if (hideYear) {
        // 10. January at 10:20
        return `${day} ${month} at ${hours}:${minutes}`;
      }

      // 10. January 2017. at 10:20
      return `${day} ${month} ${year} at ${hours}:${minutes}`;
    },
    timeAgo: function (dateParam) {
      if (!dateParam) {
        return null;
      }

      const date =
        typeof dateParam === "object" ? dateParam : new Date(dateParam);
      const DAY_IN_MS = 86400000; // 24 * 60 * 60 * 1000
      const today = new Date();
      const yesterday = new Date(today - DAY_IN_MS);
      const seconds = Math.round((today - date) / 1000);
      const minutes = Math.round(seconds / 60);
      const isToday = today.toDateString() === date.toDateString();
      const isYesterday = yesterday.toDateString() === date.toDateString();
      const isThisYear = today.getFullYear() === date.getFullYear();

      if (seconds < 5) {
        return "now";
      } else if (seconds < 60) {
        return `${seconds} seconds ago`;
      } else if (seconds < 90) {
        return "about a minute ago";
      } else if (minutes < 60) {
        return `${minutes} minutes ago`;
      } else if (isToday) {
        return this.getFormattedDate(date, "Today"); // Today at 10:20
      } else if (isYesterday) {
        return this.getFormattedDate(date, "Yesterday"); // Yesterday at 10:20
      } else if (isThisYear) {
        return this.getFormattedDate(date, false, true); // 10. January at 10:20
      }

      return this.getFormattedDate(date); // 10. January 2017. at 10:20
    },
    getPageImagePath: function (page) {
      if (page.imagen_file && page.imagen_file.file_front_path) {
        // Si file_front_path ya viene procesado desde el backend con /
        return page.imagen_file.file_front_path;
      }
      if (page.imagen_file) {
        return (
          BASEURL +
          page.imagen_file.file_path.substr(2) +
          page.imagen_file.file_name +
          "." +
          page.imagen_file.file_type
        );
      }
      return BASEURL + "public/img/default.jpg";
    },
    listQuery: function (extra, page) {
      extra = extra || {};
      var query = {};
      Object.keys(extra).forEach(function (key) {
        if (extra[key] !== null && extra[key] !== undefined && extra[key] !== "") {
          query[key] = extra[key];
        }
      });
      query.page = page || this.currentPage || 1;
      query.per_page = 25;
      if (this.filter && !this.client_search) {
        query.q = this.filter;
      }
      this.currentPage = parseInt(query.page, 10) || 1;
      return query;
    },
    applyPaginatorFromResponse: function (response) {
      if (response && response.current_page) {
        this.showPagination = true;
        this.paginator.current_page = response.current_page;
        this.paginator.per_page = response.per_page;
        this.paginator.total_rows = response.total_rows;
        this.paginator.offset = response.offset;
        this.paginator.total_pages = response.total_pages;
        this.paginator.first_page = response.first_page;
        this.paginator.last_page = response.last_page;
        this.paginator.next_page = response.next_page;
        this.paginator.prev_page = response.prev_page;
        this.currentPage = parseInt(response.current_page, 10) || 1;
        this.set_paginatorLinks();
        return;
      }
      this.showPagination = false;
    },
    set_paginatorLinks: function () {
      if (!this.showPagination) {
        return null;
      }
      var links = [];
      links.push({
        page: this.paginator.prev_page,
        label: '<i class="material-icons">chevron_left</i>',
        class: this.paginator.prev_page == 0 ? "disabled" : "waves-effect",
      });
      var pages = this.rangodepaginas(
        this.paginator.current_page - 0,
        2,
        this.paginator.total_pages
      );
      pages = this.paginaEllipsis(pages);
      if (!pages.includes(1)) {
        links.push({
          page: 1,
          label: 1,
          class: this.paginator.current_page == 1 ? "active" : "waves-effect",
        });
      }
      for (var index = 1; index <= pages.length; index++) {
        if (pages[index - 1] == "...") {
          if (index === 2) {
            links.push({
              page: parseInt(pages[index]) - 1,
              label: pages[index - 1],
              class: "waves-effect ",
            });
          } else {
            links.push({
              page: parseInt(pages[index - 2]) + 1,
              label: pages[index - 1],
              class: "waves-effect ",
            });
          }
        } else {
          links.push({
            page: pages[index - 1],
            label: pages[index - 1],
            class:
              this.paginator.current_page == pages[index - 1]
                ? "active"
                : "waves-effect",
          });
        }
      }
      if (!pages.includes(this.paginator.total_pages)) {
        links.push({
          page: this.paginator.total_pages,
          label: this.paginator.total_pages,
          class:
            this.paginator.current_page == this.paginator.total_pages
              ? "active"
              : "waves-effect",
        });
      }
      links.push({
        page: this.paginator.next_page,
        label: '<i class="material-icons">chevron_right</i>',
        class:
          this.paginator.next_page > this.paginator.total_pages
            ? "disabled"
            : "waves-effect",
      });
      this.paginatorLinks = links;
      return links;
    },
    rangodepaginas: function (actual, rango, final) {
      var desde = actual - rango,
        hasta = actual + rango,
        paginas = [];
      for (var i = 1; i <= final; i++) {
        if (i === 1 || i === final || (i >= desde && i <= hasta)) {
          paginas.push(i);
        }
      }
      return paginas;
    },
    paginaEllipsis: function (paginas) {
      if (!paginas.length) {
        return paginas;
      }
      var final = paginas[paginas.length - 1];
      var rango_con_ellipsis = [];
      for (var i = 1; i < paginas.length - 1; i++) {
        if (paginas[i - 1] === 1 && paginas[i] !== 2) {
          rango_con_ellipsis.push(1);
          rango_con_ellipsis.push("...");
        }
        rango_con_ellipsis.push(paginas[i]);
        if (paginas[i + 1] === final && paginas[i] !== final - 1) {
          rango_con_ellipsis.push("...");
          rango_con_ellipsis.push(final);
        }
      }
      return rango_con_ellipsis;
    },
    pagerTo: function (page) {
      if (typeof this.reloadList === "function") {
        this.reloadList(page);
        return;
      }
      if (typeof this.getData === "function") {
        this.getData(page);
      }
    },
    t: function (key) {
      var dict = window.ADMIN_LANG || {};
      if (dict[key]) {
        return dict[key];
      }
      return key;
    },
    lang: function (key) {
      return this.t(key);
    },
    toast: function (keyOrHtml) {
      var html = this.t(keyOrHtml);
      M.toast({ html: html });
    },
    toastError: function (xhr, response) {
      var msg = "";
      if (response && response.error_message) {
        msg = response.error_message;
      } else if (xhr && xhr.responseJSON && xhr.responseJSON.error_message) {
        msg = xhr.responseJSON.error_message;
      }
      this.toast(msg || "toast_error");
      if (xhr) {
        console.error(xhr);
      }
    },
    reinitPlugin: function (selector, Plugin, options) {
      if (!window.M || !Plugin) {
        return;
      }
      var els = document.querySelectorAll(selector);
      for (var i = 0; i < els.length; i++) {
        var inst = Plugin.getInstance(els[i]);
        if (inst && typeof inst.destroy === "function") {
          inst.destroy();
        }
      }
      if (els.length) {
        Plugin.init(els, options || {});
      }
    },
    initPlugins: function () {
      var self = this;
      this.$nextTick(function () {
        self.reinitPlugin(".collapsible:not(#slide-out)", M.Collapsible);
        self.reinitPlugin(".tooltipped", M.Tooltip);
        self.reinitPlugin(
          ".dropdown-trigger:not(.select-dropdown)",
          M.Dropdown,
          { constrainWidth: false }
        );
        self.reinitPlugin(".modal", M.Modal);
        if (M.Materialbox) {
          self.reinitPlugin(".materialboxed", M.Materialbox);
        }
      });
    },
  },
  watch: {
    filter: function () {
      var self = this;
      if (this.client_search || (!this.pagination && !this.serverPagination)) {
        return;
      }
      clearTimeout(this._searchTimer);
      this._searchTimer = setTimeout(function () {
        if (typeof self.reloadList === "function") {
          self.reloadList(1);
          return;
        }
        if (typeof self.getData === "function") {
          self.getData(1);
        }
      }, 400);
    },
  },
};

var listMixin = {
  methods: {
    listUrl: function (id) {
      var path = (this.listEndpoint || "").replace(/\/?$/, "/");
      return BASEURL + path + (typeof id === "undefined" ? "" : id);
    },
    reloadList: function (page) {
      this.fetchList(page);
    },
    wrapListItem: function (item) {
      if (item && item.user) {
        item.user = new User(item.user);
      }
      return item;
    },
    listExtraQuery: function () {
      return {};
    },
    fetchList: function (page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "GET",
        url: self.listUrl(),
        data: this.listQuery(this.listExtraQuery(), page),
        dataType: "json",
        success: function (response) {
          var items = response && response.data ? response.data : [];
          if (!Array.isArray(items)) {
            items = Object.keys(items).map(function (k) {
              return items[k];
            });
          }
          self[self.listKey] = items.map(function (item) {
            return self.wrapListItem(item);
          });
          self.applyPaginatorFromResponse(response);
          self.loader = false;
          self.initPlugins();
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
    deleteListItem: function (item, index) {
      var self = this;
      var id = item && this.listPk ? item[this.listPk] : null;
      if (!id && item) {
        id = item.id || item.video_id;
      }
      if (!id) {
        return;
      }
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: self.listUrl(id),
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("toast_deleted");
            if (self.serverPagination) {
              self.fetchList();
              return;
            }
            if (typeof index === "number" && self[self.listKey]) {
              self[self.listKey].splice(index, 1);
            }
            self.loader = false;
            self.initPlugins();
            return;
          }
          self.loader = false;
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback: function (data) {
      if (data) {
        this.deleteListItem(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
  },
};

var formMixin = {
  methods: {
    statusValue: function () {
      return this.status ? 1 : 2;
    },
    afterSave: function (response) {
      this.editMode = true;
      if (this.formIdField && response && response.data) {
        this[this.formIdField] = response.data[this.formIdField];
      }
    },
    runSaveData: function (callBack) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + self.formEndpoint,
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          if (self.debug) {
            console.log(self.formEndpoint, response);
          }
          if (response.code == 200) {
            self.afterSave(response);
            self.loader = false;
            if (typeof callBack === "function") {
              callBack(response);
            }
          } else {
            self.loader = false;
            self.toastError(null, response);
          }
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
  },
};

formsElements = [
  {
    field_name: "title",
    displayName: "Title",
    icon: "format_color_text",
    component: "formFieldTitle",
    status: "1",
    data: {},
  },
  {
    field_name: "text",
    displayName: "Text",
    icon: "short_text",
    component: "formFieldTextArea",
    status: "1",
    data: {},
  },
  {
    field_name: "formatText",
    displayName: "Formatted Text",
    component: "formTextFormat",
    icon: "format_size",
    status: "1",
    data: {},
  },
  {
    field_name: "image",
    displayName: "Image",
    component: "formImageSelector",
    icon: "image",
    status: "1",
    data: {},
  },
  {
    field_name: "date",
    displayName: "Date",
    component: "formFieldDate",
    icon: "date_range",
    status: "1",
    data: {},
  },
  {
    field_name: "time",
    displayName: "Time",
    component: "formFieldTime",
    icon: "access_time",
    status: "1",
    data: {},
  },
  {
    field_name: "number",
    displayName: "Number",
    component: "formFieldNumber",
    icon: "looks_one",
    status: "1",
    data: {},
  },
  {
    field_name: "dropdown_select",
    displayName: "Select",
    component: "formFieldSelect",
    status: "1",
    icon: "list",
    data: {},
  },
  {
    field_name: "bolean",
    displayName: "Boolean",
    component: "formFieldBoolean",
    status: "1",
    icon: "check_circle",
    data: {},
  },
];

// Definición de la clase "User"
class User {
  // Propiedades de la clase
  user_id = null;
  username = "";
  email = "";
  lastseen = "";
  level = "";
  role = "";
  status = "";
  usergroup_id;
  user_data = {
    nombre: "",
    apellido: "",
    direccion: "",
    telefono: "",
    "create by": "",
  };

  // Constructor de la clase
  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || ""; // Asigna los valores recibidos a las propiedades de la clase
      }
    }
  }

  // Método de la clase que retorna el nombre completo del usuario
  get_fullname = () => {
    if (this.user_data.nombre && this.user_data.apellido) {
      return this.user_data.nombre + " " + this.user_data.apellido;
    } else {
      return "";
    }
  };

  // Método de la clase que retorna la URL del perfil del usuario
  get_profileurl = () => {
    return BASEURL + "admin/users/ver/" + this.user_id;
  };

  // Método de la clase que retorna la URL del avatar del usuario
  get_avatarurl = () => {
    if (this.user_data.avatar) {
      return BASEURL + this.user_data.avatar;
    } else {
      return BASEURL + "public/img/profile/default.png";
    }
  };

  // Método de la clase que retorna la URL de edición del usuario
  get_edit_url = () => {
    return BASEURL + "admin/users/edit/" + this.user_id;
  };
}

class Page {
  // Propiedades de la clase
  page_id = null;
  categorie_id = "";
  content = "";
  date_create = "";
  date_publish = "";
  date_update = "";
  layout = "";
  mainImage = null;
  model_type = "";
  page_type_id = "";
  path = "";
  status = "";
  subcategorie_id = "";
  subtitle = "";
  template = "";
  title = "";
  user = new User();
  user_id = "";
  visibility = "";

  // Constructor de la clase
  constructor(params) {
    // Recorre los parametros del objeto params y le asigna el valor al atributo correspondiente
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || "";
      }
    }
  }

  // Método para obtener una versión resumida del contenido de la página
  getcontentText = function () {
    var span = document.createElement("span");
    span.innerHTML = this.content;
    let text = span.textContent || span.innerText;
    return text.substring(0, 220) + "...";
  };

  // Método para obtener la ruta de la imagen principal de la página
  getPageImagePath() {
    if (this.imagen_file && this.imagen_file.file_front_path) {
      // Si file_front_path ya viene procesado desde el backend con /
      return this.imagen_file.file_front_path;
    }
    if (this.imagen_file) {
      return (
        BASEURL +
        this.imagen_file.file_path.substr(2) +
        this.imagen_file.file_name +
        "." +
        this.imagen_file.file_type
      );
    }
    return BASEURL + "public/img/default.jpg";
  }

  // Método para obtener la ruta completa de la página
  getPageFullPath = function () {
    if (this.status == 1) {
      return BASEURL + this.path;
    }
    return BASEURL + "admin/pages/editar/" + this.page_id;
  };
}

class ExplorerFile {
  date_create = ""; // fecha de creación del archivo
  date_update = ""; // fecha de actualización del archivo
  featured = ""; // marca de destacado del archivo
  file_id = ""; // identificador del archivo
  file_name = ""; // nombre del archivo
  file_path = ""; // ruta del archivo
  file_type = ""; // tipo de archivo
  parent_name = ""; // nombre del directorio padre
  rand_key = ""; // clave aleatoria
  share_link = ""; // enlace para compartir el archivo
  shared_user_group_id = ""; // identificador del grupo de usuarios compartidos
  status = ""; // estado del archivo
  user_id = ""; // identificador del usuario propietario
  user = new User(); // objeto usuario asociado al archivo

  constructor(params) {
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param] || "";
      }
    }
  }

  // devuelve el nombre completo del archivo (nombre + extensión)
  get_filename = () => {
    return this.file_name + "." + this.file_type;
  };

  // devuelve la ruta relativa del archivo
  get_relative_file_path = () => {
    return this.file_path + this.get_filename();
  };

  // devuelve la ruta completa del archivo (con el nombre completo y la URL base)
  get_full_file_path = () => {
    return BASEURL + this.file_path + this.get_filename();
  };

  // devuelve la ruta completa para compartir el archivo (con la URL base)
  get_full_share_path = () => {
    return BASEURL + this.share_link;
  };

  // devuelve la clase de icono correspondiente al tipo de archivo
  get_icon() {
    let icon = "far fa-file";
    switch (this.file_type) {
      case "folder":
        icon = "far fa-folder";
        break;
      case "jpg":
      case "png":
      case "gif":
        icon = "fas fa-file-image";
        break;
      case "html":
        icon = "fab fa-html5";
        break;
      case "scss":
        icon = "fab fa-sass";
        break;
      case "css":
      case "min.css":
        icon = "fab fa-css3-alt";
        break;
      case "txt":
        icon = "far fa-file-alt";
        break;
      case "php":
      case "blade.php":
        icon = "fab fa-php";
        break;
      case "js":
      case "json":
      case "min.js":
        icon = "fab fa-js";
        break;
      case "eot":
      case "otf":
      case "woff2":
        icon = "fas fa-font";
        break;
    }
    return icon;
  }

  // devuelve verdadero si el archivo es una imagen
  is_image() {
    if (
      this.file_type == "jpg" ||
      this.file_type == "png" ||
      this.file_type == "gif"
    ) {
      return true;
    }
    return false;
  }
}

// Definición de la clase Config_data
class Config_data {
  // Propiedades de la clase
  type_value = "string";
  validate_as = "text";
  max_lenght = "120";
  min_lenght = "0";
  handle_as = "input";
  input_type = "text";
  perm_values = null;

  // Constructor de la clase
  constructor(params) {
    // Ciclo for para asignar las propiedades pasadas como argumento
    for (const param in params) {
      if (params.hasOwnProperty(param)) {
        this[param] = params[param];
      }
    }

    // Switch statement para definir el valor de la propiedad "handle_as" basado en el valor de "type_value"
    switch (this.type_value) {
      case "boolean":
        this.handle_as = "switch";
        break;

      case "number":
        this.handle_as = "input";
        this.input_type = "number";
        break;

      default:
        break;
    }
  }
}

function showRefreshUI(registration) {
  // TODO: Display a toast or refresh UI.
  document.getElementsByTagName("body")[0].insertAdjacentHTML(
    "beforeend",
    `<div id = "update-app-zone" >
       <div class="card blue-grey darken-1">
        <div class="card-content white-text">
          <p><span>A new version is available, click here to update</span></p>
        </div>
        <div class="card-action">
          <a href="#"><i class="fas fa-redo"></i> Update</a>
        </div>
      </div>
      </div>`
  );
  const element = document.querySelector("#update-app-zone a");
  element.addEventListener("click", function () {
    if (!registration.waiting) {
      // Just to ensure registration.waiting is available before
      // calling postMessage()
      return;
    }
    if (window.location.href.includes("admin/login")) {
      localStorage.clear();
      const cookies = document.cookie.split(";");
      for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
      }
    }
    registration.waiting.postMessage("skipWaiting");
  });
}

function onNewServiceWorker(registration, callback) {
  if (registration.waiting) {
    // SW is waiting to activate. Can occur if multiple clients open and
    // one of the clients is refreshed.
    return callback();
  }

  function listenInstalledStateChange() {
    registration.installing.addEventListener("statechange", function (event) {
      if (event.target.state === "installed") {
        // A new service worker is available, inform the user
        callback();
      }
    });
  }

  if (registration.installing) {
    return listenInstalledStateChange();
  }

  // We are currently controlled so a new SW may be found...
  // Add a listener in case a new SW is found,
  registration.addEventListener("updatefound", listenInstalledStateChange);
}

if (navigator.serviceWorker) {
  window.addEventListener("load", async function () {
    let refreshing;
    // When the user asks to refresh the UI, we'll need to reload the window
    navigator.serviceWorker.addEventListener(
      "controllerchange",
      function (event) {
        if (refreshing) return; // prevent infinite refresh loop when you use "Update on Reload"
        refreshing = true;
        window.location.reload();
      }
    );

    navigator.serviceWorker
      .register("/sw.js", {
        scope: BASEURL + "admin/",
      })
      .then(function (registration) {
        // Track updates to the Service Worker.
        if (!navigator.serviceWorker.controller) {
          // The window client isn't currently controlled so it's a new service
          // worker that will activate immediately
          return;
        }
        registration.update();

        onNewServiceWorker(registration, function () {
          showRefreshUI(registration);
        });
      });
  });
}

Vue.component("userInfo", {
  template: `
  <div class="collection form-user-component user-info-inline">
    <div class="collection-item avatar">
      <a :href="user.get_profileurl()">
        <img :src="user.get_avatarurl()" alt="" class="circle profile-img">
        <span class="title">{{user.get_fullname()}}</span>
        <div>{{user.role ? user.role : (user.usergroup ? user.usergroup.name : '')}}</div>
      </a>
    </div>
  </div>
  `,
  props: ["user"],
});

Vue.component("preloader", {
  template: `
  <div class="preloader-wrapper big active">
      <div class="spinner-layer spinner-blue-only">
          <div class="circle-clipper left">
              <div class="circle"></div>
          </div>
          <div class="gap-patch">
              <div class="circle"></div>
          </div>
          <div class="circle-clipper right">
              <div class="circle"></div>
          </div>
      </div>
  </div>
  `,
});

Vue.component("confirmModal", {
  props: ["id", "title"],
  template: `
    <div :id="id" class="modal confirm-modal">
    <div class="modal-content">
          <div class="modal-header">
            <div class="row">
              <div class="col s12">
                <h4>{{title}}</h4>
              </div>
            </div>
          </div>
          <div class="row">
              <div class="col s12">
                  <slot></slot>
              </div>
          </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="modal-action modal-close waves-effect waves-red btn" @click="onClickButton(false);">Cancel</button>
          <button type="button" class="modal-close waves-effect waves-green btn red" @click="onClickButton(true);">Accept</button>
      </div>
  </div>
  `,
  methods: {
    onClickButton(result) {
      this.$emit("notify", result);
    },
  },
  mounted() {
    // Inicializa el modal de Materialize al montar el componente
    if (window.M && this.$el && this.$el.classList.contains('modal')) {
      window.M.Modal.init(this.$el, {});
    }
  },
});

Vue.component("preview", {
  props: ["url"],
  data() {
    return {
      fullScreen: false,
      previewUrl: "",
    };
  },
  template: `
  <div class="preview-container" v-bind:class="{fixed: fullScreen}">
    <div class="preview-options">
    <input type="text" name="url" id="url" readonly :value="previewUrl">
    <a href="#!" class="option"><i class="material-icons" v-on:click="expand();">aspect_ratio</i></a>
    <a :href="previewUrl" class="option" target="_blank"><i class="material-icons">open_in_new</i></a>
    </div>
    <iframe class="responsive-iframe" :src="previewUrl"></iframe>
  </div>
  `,
  watch: {
    url: function (val) {
      this.previewUrl = val;
    },
  },
  mounted() {
    this.$nextTick(function () {
      this.previewUrl = this.url;
    });
  },
  methods: {
    togglePreview() {
      this.fullScreen = !this.fullScreen;
    },
    expand() {
      this.$emit("expand");
    },
  },
});
