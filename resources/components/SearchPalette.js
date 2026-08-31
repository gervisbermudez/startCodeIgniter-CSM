window.SearchMapper = (function () {
  var MAX_PALETTE = 20;

  function asArray(value) {
    if (!value) {
      return [];
    }
    if (Array.isArray(value)) {
      return value;
    }
    if (typeof value === "object") {
      return Object.keys(value).map(function (k) {
        return value[k];
      });
    }
    return [];
  }

  function stripHtml(html, length) {
    if (!html) {
      return "";
    }
    if (typeof html !== "string") {
      html = String(html);
    }
    var span = document.createElement("span");
    span.innerHTML = html;
    var text = (span.textContent || span.innerText || "")
      .replace(/\s+/g, " ")
      .trim();
    if (!length) {
      return text;
    }
    if (text.length > length) {
      return text.substring(0, length) + "…";
    }
    return text;
  }

  function userTitle(user) {
    if (!user) {
      return "";
    }
    var data = user.user_data || {};
    var full = ((data.nombre || "") + " " + (data.apellido || "")).trim();
    return full || user.username || user.email || "";
  }

  function fileTitle(file) {
    var ext =
      file.file_type && file.file_type !== "folder" ? "." + file.file_type : "";
    return (file.file_name || "") + ext;
  }

  function fileIcon(file) {
    if (!file || file.file_type === "folder") {
      return "folder";
    }
    var t = (file.file_type || "").toLowerCase();
    if (["jpg", "jpeg", "png", "gif", "webp", "svg"].indexOf(t) !== -1) {
      return "image";
    }
    if (["mp4", "webm", "mov"].indexOf(t) !== -1) {
      return "movie";
    }
    if (["mp3", "wav", "ogg"].indexOf(t) !== -1) {
      return "audiotrack";
    }
    if (t === "pdf") {
      return "picture_as_pdf";
    }
    if (["js", "json", "php", "css", "html", "scss"].indexOf(t) !== -1) {
      return "code";
    }
    return "insert_drive_file";
  }

  function statusKey(status) {
    var n = parseInt(status, 10);
    if (n === 1) {
      return "published";
    }
    if (n === 2) {
      return "draft";
    }
    if (n === 3) {
      return "archived";
    }
    if (n === 0) {
      return "deleted";
    }
    return "";
  }

  function siteformSubmitTitle(item) {
    var formName =
      (item.SiteForm && item.SiteForm.name) ||
      (item.siteform && item.siteform.name) ||
      "";
    if (formName) {
      return formName;
    }
    return "#" + (item.siteform_submit_id || "");
  }

  function contentTitle(item) {
    if (item.title) {
      return item.title;
    }
    if (item.custom_model && item.custom_model.form_name) {
      return item.custom_model.form_name;
    }
    return item.model_type || "";
  }

  var groups = [
    {
      key: "pages",
      type: "pages",
      icon: "description",
      href: function (i) {
        return "admin/pages/editar/" + i.page_id;
      },
      title: function (i) {
        return i.title || "";
      },
      snippet: function (i) {
        return stripHtml(i.content || i.subtitle || "", 80);
      },
    },
    {
      key: "users",
      type: "users",
      icon: "person",
      href: function (i) {
        return "admin/users/ver/" + i.user_id;
      },
      title: userTitle,
      snippet: function (i) {
        return i.email || i.username || "";
      },
    },
    {
      key: "files",
      type: "files",
      icon: fileIcon,
      href: function () {
        return "admin/files";
      },
      title: fileTitle,
      snippet: function (i) {
        return i.file_path || "";
      },
    },
    {
      key: "albumes",
      type: "albums",
      icon: "photo_library",
      href: function (i) {
        return "admin/gallery/items/" + i.album_id;
      },
      title: function (i) {
        return i.name || "";
      },
      snippet: function (i) {
        return stripHtml(i.description || "", 80);
      },
    },
    {
      key: "categories",
      type: "categories",
      icon: "label",
      href: function (i) {
        return "admin/categories/editar/" + i.categorie_id;
      },
      title: function (i) {
        return i.name || "";
      },
      snippet: function (i) {
        return stripHtml(i.description || i.type || "", 80);
      },
    },
    {
      key: "form_customs",
      type: "models",
      icon: "view_module",
      href: function (i) {
        return "admin/custommodels/editForm/" + i.custom_model_id;
      },
      title: function (i) {
        return i.form_name || "";
      },
      snippet: function (i) {
        return stripHtml(i.form_description || i.model_type || "", 80);
      },
    },
    {
      key: "form_contents",
      type: "contents",
      icon: "article",
      href: function (i) {
        return (
          "admin/custommodels/editData/" +
          i.custom_model_id +
          "/" +
          i.custom_model_content_id
        );
      },
      title: contentTitle,
      snippet: function (i) {
        return i.date_create || "";
      },
    },
    {
      key: "siteforms",
      type: "siteforms",
      icon: "assignment",
      href: function (i) {
        return "admin/siteforms/editar/" + i.siteform_id;
      },
      title: function (i) {
        return i.name || "";
      },
      snippet: function (i) {
        return i.template || "";
      },
    },
    {
      key: "siteform_submits",
      type: "submissions",
      icon: "inbox",
      href: function (i) {
        return "admin/siteforms/submit/#/details/" + i.siteform_submit_id;
      },
      title: siteformSubmitTitle,
      snippet: function (i) {
        return i.date_create || "";
      },
    },
    {
      key: "menus",
      type: "menus",
      icon: "view_list",
      href: function (i) {
        return "admin/menus/editar/" + i.menu_id;
      },
      title: function (i) {
        return i.name || "";
      },
      snippet: function (i) {
        return i.position || i.template || "";
      },
    },
  ];

  function i18nLabel(i18n, type) {
    var key = "type_" + type;
    if (i18n && i18n[key]) {
      return i18n[key];
    }
    return type;
  }

  function flatten(data, i18n, limit) {
    var hits = [];
    var payload = data || {};
    groups.forEach(function (g) {
      asArray(payload[g.key]).forEach(function (item, idx) {
        hits.push({
          id: g.type + "-" + (item.page_id || item.user_id || item.file_id || item.album_id || item.categorie_id || item.custom_model_id || item.custom_model_content_id || item.siteform_id || item.siteform_submit_id || item.menu_id || idx),
          type: g.type,
          typeLabel: i18nLabel(i18n, g.type),
          icon: typeof g.icon === "function" ? g.icon(item) : g.icon,
          title: g.title(item) || i18nLabel(i18n, g.type),
          snippet: g.snippet(item),
          href: (typeof BASEURL !== "undefined" ? BASEURL : "") + g.href(item),
          status: statusKey(item.status),
        });
      });
    });
    if (limit) {
      return hits.slice(0, limit);
    }
    return hits;
  }

  function emptyPayload() {
    return {
      pages: [],
      users: [],
      files: [],
      form_customs: [],
      form_contents: [],
      siteforms: [],
      siteform_submits: [],
      menus: [],
      categories: [],
      albumes: [],
    };
  }

  return {
    flatten: flatten,
    asArray: asArray,
    emptyPayload: emptyPayload,
    MAX_PALETTE: MAX_PALETTE,
    stripHtml: stripHtml,
  };
})();

Vue.component("searchHit", {
  props: ["hit", "active"],
  computed: {
    statusLabel: function () {
      var i18n = window.SEARCH_I18N || {};
      if (!this.hit.status) {
        return "";
      }
      return i18n["status_" + this.hit.status] || "";
    },
  },
  template:
    '<a class="search-hit" :class="{ active: active }" :href="hit.href">' +
    '<i class="material-icons search-hit__icon" aria-hidden="true">{{ hit.icon }}</i>' +
    '<span class="search-hit__body">' +
    '<span class="search-hit__title truncate">{{ hit.title }}</span>' +
    '<span class="search-hit__snippet truncate" v-if="hit.snippet">{{ hit.snippet }}</span>' +
    "</span>" +
    '<span class="search-hit__meta">' +
    '<span class="search-hit__type">{{ hit.typeLabel }}</span>' +
    '<span class="search-status" :class="\'search-status--\' + hit.status" v-if="statusLabel">{{ statusLabel }}</span>' +
    "</span>" +
    "</a>",
});

var SearchPalette = new Vue({
  el: "#search-palette",
  data: {
    open: false,
    query: "",
    loader: false,
    hits: [],
    typeFilter: "all",
    selectedIndex: 0,
    i18n: window.SEARCH_I18N || {},
    debounceTimer: null,
    requestSeq: 0,
  },
  computed: {
    queryTrimmed: function () {
      return (this.query || "").trim();
    },
    visibleHits: function () {
      if (this.typeFilter === "all") {
        return this.hits;
      }
      var self = this;
      return this.hits.filter(function (hit) {
        return hit.type === self.typeFilter;
      });
    },
    chipTypes: function () {
      var counts = {};
      this.hits.forEach(function (hit) {
        counts[hit.type] = (counts[hit.type] || 0) + 1;
      });
      var chips = [
        { id: "all", label: this.i18n.type_all || "All", count: this.hits.length },
      ];
      var self = this;
      Object.keys(counts).forEach(function (type) {
        chips.push({
          id: type,
          label: self.i18n["type_" + type] || type,
          count: counts[type],
        });
      });
      return chips;
    },
    showMinChars: function () {
      return this.queryTrimmed.length === 1;
    },
    showIdle: function () {
      return this.queryTrimmed.length === 0;
    },
    showNoResults: function () {
      return this.queryTrimmed.length >= 2 && this.visibleHits.length === 0;
    },
    noResultsLabel: function () {
      var tpl = this.i18n.noResults || "%s";
      return tpl.replace("%s", this.queryTrimmed);
    },
    resultsUrl: function () {
      return (
        (typeof BASEURL !== "undefined" ? BASEURL : "") +
        "admin/search/?q=" +
        encodeURIComponent(this.queryTrimmed)
      );
    },
    activeDescendant: function () {
      if (!this.visibleHits.length) {
        return null;
      }
      return "search-hit-" + this.selectedIndex;
    },
  },
  watch: {
    query: function () {
      this.scheduleSearch();
    },
    visibleHits: function () {
      this.selectedIndex = 0;
    },
    open: function (isOpen) {
      document.body.classList.toggle("search-palette-open", isOpen);
    },
  },
  methods: {
    openPalette: function () {
      var self = this;
      this.open = true;
      this.$nextTick(function () {
        if (self.$refs.queryInput) {
          self.$refs.queryInput.focus();
          self.$refs.queryInput.select();
        }
      });
    },
    closePalette: function () {
      this.open = false;
    },
    togglePalette: function () {
      if (this.open) {
        this.closePalette();
      } else {
        this.openPalette();
      }
    },
    clearQuery: function () {
      this.query = "";
      this.hits = [];
      this.typeFilter = "all";
      this.openPalette();
    },
    setTypeFilter: function (id) {
      this.typeFilter = id;
      this.selectedIndex = 0;
    },
    scheduleSearch: function () {
      var self = this;
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
      }
      if (this.queryTrimmed.length < 2) {
        this.hits = [];
        this.loader = false;
        return;
      }
      this.debounceTimer = setTimeout(function () {
        self.performSearch();
      }, 300);
    },
    performSearch: function () {
      var self = this;
      var seq = ++this.requestSeq;
      this.loader = true;
      $.ajax({
        type: "GET",
        url:
          BASEURL +
          "api/v1/search/?q=" +
          encodeURIComponent(this.queryTrimmed),
        dataType: "json",
        success: function (response) {
          if (seq !== self.requestSeq) {
            return;
          }
          var data =
            response && response.data
              ? response.data
              : window.SearchMapper.emptyPayload();
          self.hits = window.SearchMapper.flatten(
            data,
            self.i18n,
            window.SearchMapper.MAX_PALETTE
          );
          self.loader = false;
          self.selectedIndex = 0;
        },
        error: function () {
          if (seq !== self.requestSeq) {
            return;
          }
          self.loader = false;
          self.hits = [];
          M.toast({ html: self.i18n.error || "Error" });
        },
      });
    },
    move: function (delta) {
      var len = this.visibleHits.length;
      if (!len) {
        return;
      }
      this.selectedIndex = (this.selectedIndex + delta + len) % len;
      this.scrollActiveIntoView();
    },
    scrollActiveIntoView: function () {
      var el = document.getElementById("search-hit-" + this.selectedIndex);
      if (el && el.scrollIntoView) {
        el.scrollIntoView({ block: "nearest" });
      }
    },
    activate: function () {
      var hit = this.visibleHits[this.selectedIndex];
      if (hit && hit.href) {
        window.location.href = hit.href;
        return;
      }
      if (this.queryTrimmed) {
        window.location.href = this.resultsUrl;
      }
    },
    isSearchHotkey: function (e) {
      var isK =
        e.key === "k" ||
        e.key === "K" ||
        e.code === "KeyK" ||
        e.keyCode === 75 ||
        e.which === 75;
      return (e.ctrlKey || e.metaKey) && isK && !e.altKey;
    },
    onShellKeydown: function (e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        e.preventDefault();
        this.closePalette();
        return;
      }
      if (e.key === "ArrowDown" || e.keyCode === 40) {
        e.preventDefault();
        this.move(1);
        return;
      }
      if (e.key === "ArrowUp" || e.keyCode === 38) {
        e.preventDefault();
        this.move(-1);
        return;
      }
      if (e.key === "Enter" || e.keyCode === 13) {
        if (e.target && e.target.id === "search-palette-input") {
          e.preventDefault();
          this.activate();
        }
      }
    },
    onGlobalKey: function (e) {
      if (e.repeat) {
        return;
      }
      if (this.isSearchHotkey(e)) {
        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) {
          e.stopImmediatePropagation();
        }
        this.togglePalette();
        return;
      }
      if (!this.open) {
        return;
      }
      if (e.key === "Escape" || e.keyCode === 27) {
        e.preventDefault();
        this.closePalette();
      }
    },
    onDocumentClick: function (e) {
      var trigger = e.target.closest
        ? e.target.closest("[data-search-palette-trigger]")
        : null;
      if (trigger) {
        e.preventDefault();
        this.openPalette();
      }
    },
  },
  mounted: function () {
    var self = this;
    if (this.$el && this.$el.parentNode !== document.body) {
      document.body.appendChild(this.$el);
    }
    this._onGlobalKey = function (e) {
      self.onGlobalKey(e);
    };
    this._onDocumentClick = function (e) {
      self.onDocumentClick(e);
    };
    document.addEventListener("keydown", this._onGlobalKey, true);
    document.addEventListener("click", this._onDocumentClick);
    window.SearchPalette = this;
  },
  beforeDestroy: function () {
    document.removeEventListener("keydown", this._onGlobalKey, true);
    document.removeEventListener("click", this._onDocumentClick);
  },
});
