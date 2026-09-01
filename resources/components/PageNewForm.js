var trumbowygInstance = null;

// Helper: debounce para optimizar autosave
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

var PageNewForm = new Vue({
  el: "#PageNewForm-root",
  data: {
    debug: DEBUGMODE,
    bootLoading: true,
    bootPending: 0,
    bootFailed: false,
    saving: false,
    editorReady: false,
    editMode: false,
    page_id: null,
    user: null,
    form: new VueForm({
      title: {
        value: null,
        required: true,
        type: "username",
        maxLength: 120,
        minLength: 5,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
      subtitle: {
        value: null,
        required: false,
        type: "username",
        maxLength: 120,
        minLength: 5,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
    }),
    status: false,
    path: "",
    content: "Content of the page",
    json_content: [],
    visibility: 1,
    publishondate: true,
    datepublish: "",
    timepublish: "",
    template: "default",
    layout: "default",
    categorie_id: "0",
    subcategorie_id: "0",
    page_type_id: "1",
    layouts: [],
    mainImage: [], // index = 0 mainImage , index = 1 thumbnailImage
    templates: [],
    pageTypes: [],
    categories: [],
    subcategories: [],
    page_data: {
      title: "",
    },
    customMetas: [],
    metas: [
      {
        name: "author",
        content: new User(
          JSON.parse(localStorage.getItem("userdata"))
        ).get_fullname(),
      },
      {
        name: "keywords",
        content: "",
      },
      {
        name: "description",
        content: "",
      },
      {
        name: "ROBOTS",
        content: "NOODP",
      },
      {
        name: "GOOGLEBOT",
        content: "INDEX, FOLLOW",
      },
      {
        property: "og:title",
        content: "",
      },
      {
        property: "og:description",
        content: "",
      },
      {
        property: "og:site_name",
        content: SITE_TITLE,
      },
      {
        property: "og:url",
        content: "",
      },
      {
        property: "og:image",
        content: "",
      },
      {
        property: "og:type",
        content: "article",
      },
      {
        name: "twitter:card",
        content: "summary_large_image",
      },
      {
        name: "twitter:site",
        content: "",
      },
      {
        name: "twitter:creator",
        content: "",
      },
      {
        name: "twitter:site",
        content: "",
      },
      {
        name: "twitter:title",
        content: "",
      },
      {
        name: "twitter:description",
        content: "",
      },
      {
        name: "twitter:image",
        content: "",
      },
    ],
    modalCallbackMode: "copyCallcack", //Or insertImage
    embedProductTabs: [
      { key: "form", helper: "render_form" },
      { key: "fragment", helper: "fragment" },
      { key: "menu", helper: "render_menu" },
      { key: "album", helper: "render_album" },
      { key: "video", helper: "render_video" },
      { key: "event", helper: "render_event" },
    ],
    embedLists: {
      form: [],
      fragment: [],
      menu: [],
      album: [],
      video: [],
      event: [],
    },
    embedLoaded: {
      form: false,
      fragment: false,
      menu: false,
      album: false,
      video: false,
      event: false,
    },
    embedLoading: {
      form: false,
      fragment: false,
      menu: false,
      album: false,
      video: false,
      event: false,
    },
    embedErrors: {
      form: "",
      fragment: "",
      menu: "",
      album: "",
      video: "",
      event: "",
    },
    embedEndpoints: {
      form: "api/v1/siteforms",
      fragment: "api/v1/fragments",
      menu: "api/v1/menus",
      album: "api/v1/albumes",
      video: "api/v1/videos",
      event: "api/v1/events",
    },
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      return (!!this.form.fields.title.value && !!this.path) || false;
    },
    getDateTimePublish: function () {
      return this.datepublish && this.timepublish
        ? this.datepublish + " " + this.timepublish + ":00"
        : null;
    },
    full_path: function () {
      return this.status ? BASEURL + this.path : "";
    },
    preview_link: function () {
      return this.page_id
        ? BASEURL + "admin/pages/preview?page_id=" + this.page_id
        : "";
    },
    getMainImagenPath() {
      if (this.mainImage.length > 0) {
        return this.mainImage[0].file_id;
      }
      return null;
    },
    getThumbnailImagePath() {
      if (this.mainImage.length > 1) {
        return this.mainImage[1].file_id;
      }
      return null;
    },
    getPagePath() {
      let segments = this.getPathSegments().filter((value, index) => {
        return value.length > 0;
      });
      segments = segments.map((value, index) => {
        return this.string_to_slug(value);
      });

      let fullPath = segments.join("/");
      this.setMetaContent(BASEURL + fullPath, "og:url");

      return fullPath;
    },
    visibleEmbedTabs: function () {
      var self = this;
      return this.embedProductTabs.filter(function (tab) {
        return self.embedCanShow(tab.key);
      });
    },
  },
  watch: {
    content: function (value) {
      var span = document.createElement("span");
      span.innerHTML = value;
      let text = span.textContent || span.innerText;
      text = text.replace(/\s+/g, " ").trim();
      this.setMetaContent(text, "description");
      this.setMetaContent(text, "og:description");
      this.setMetaContent(text, "twitter:description");
    },
    "form.fields.title.value": function (value) {
      if (!this.path) {
        this.setPath(value);
      }
    },
    publishondate: function (value) {
      if (value) {
        this.datepublish = "";
        this.timepublish = "";
      }
    },
  },
  created() {
    // Crear versión debounced del autosave (ejecuta 1 vez después de 2 segundos de inactividad)
    this.debouncedAutoSave = debounce(() => {
      if (!this.status) {
        this.runSaveData();
        this.debug ? console.log("Auto-save ejecutado") : null;
      }
    }, 2000);
  },
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    beginBoot: function (count) {
      this.bootPending = count;
      this.bootLoading = true;
      this.bootFailed = false;
    },
    finishBootRequest: function (ok) {
      var self = this;
      if (!ok && !this.bootFailed) {
        this.bootFailed = true;
        this.toast("toast_error");
      }
      this.bootPending -= 1;
      if (this.bootPending <= 0) {
        this.bootLoading = false;
        this.$nextTick(function () {
          if (!self.editorReady) {
            self.initEditor();
            self.editorReady = true;
          }
          self.initPlugins();
          M.updateTextFields();
        });
      }
    },
    setModalMode(mode) {
      this.modalCallbackMode = mode;
    },
    lang: function (key) {
      if (typeof this.t === "function") {
        return this.t(key);
      }
      var dict = window.ADMIN_LANG || {};
      return dict[key] ? dict[key] : key;
    },
    isEmbedPublished: function (row) {
      if (!row) {
        return false;
      }
      if (row.status === undefined || row.status === null || row.status === "") {
        return true;
      }
      return String(row.status) === "1";
    },
    normalizeEmbedRows: function (payload) {
      var rows = payload;
      if (!rows) {
        return [];
      }
      if (!Array.isArray(rows)) {
        if (Array.isArray(rows.items)) {
          rows = rows.items;
        } else if (Array.isArray(rows.data)) {
          rows = rows.data;
        } else {
          return [];
        }
      }
      return rows;
    },
    embedItemName: function (item, listKey) {
      if (!item) {
        return "";
      }
      if (listKey === "video") {
        return item.nam || item.nombre || item.name || "";
      }
      return item.name || "";
    },
    embedItemInsertValue: function (item, listKey) {
      if (!item) {
        return "";
      }
      if (listKey === "album" && item.album_id) {
        return String(item.album_id);
      }
      if (listKey === "video" && item.video_id) {
        return String(item.video_id);
      }
      return this.embedItemName(item, listKey);
    },
    embedCanShow: function (listKey) {
      var perms = window.PAGE_EMBED_PERMS || {};
      if (listKey === "album") {
        return perms.gallery !== false;
      }
      if (listKey === "video") {
        return perms.videos !== false;
      }
      if (listKey === "fragment") {
        return perms.fragment !== false;
      }
      return true;
    },
    embedItemKey: function (tab, item, index) {
      if (!item) {
        return tab.key + "-" + index;
      }
      return (
        tab.key +
        "-" +
        (item.album_id ||
          item.video_id ||
          item.event_id ||
          item.siteform_id ||
          item.fragment_id ||
          item.menu_id ||
          index)
      );
    },
    embedCreateUrl: function (listKey) {
      var paths = {
        album: "admin/gallery/new",
        video: "admin/videos/new",
        event: "admin/events/add",
        fragment: "admin/fragments/new",
      };
      return paths[listKey] ? BASEURL + paths[listKey] : "";
    },
    isEmbedImageFile: function (file) {
      var type = file && file.file_type ? String(file.file_type).toLowerCase() : "";
      return (
        type === "jpg" ||
        type === "jpeg" ||
        type === "png" ||
        type === "gif" ||
        type === "webp"
      );
    },
    saveEditorRange: function () {
      var $editor = $("#editor");
      try {
        if (typeof $editor.trumbowyg === "function") {
          $editor.trumbowyg("saveRange");
        }
      } catch (error) {
        this.debug ? console.error(error) : null;
      }
      trumbowygInstance = $editor.data("trumbowyg") || trumbowygInstance;
    },
    editorContainsNode: function (instance, node) {
      var editorEl = instance && instance.$ed && instance.$ed[0];
      return !!(node && node.parentNode && editorEl && editorEl.contains(node));
    },
    insertEditorNode: function (node) {
      var $editor = $("#editor");
      var inserted = false;
      try {
        if (typeof $editor.trumbowyg === "function") {
          $editor.trumbowyg("restoreRange");
        }
        var instance = trumbowygInstance || $editor.data("trumbowyg");
        if (instance && instance.$ed && instance.$ed[0]) {
          instance.$ed[0].focus();
        }
        if (instance && instance.range) {
          instance.range.insertNode(node);
          if (this.editorContainsNode(instance, node)) {
            if (instance.range.setStartAfter) {
              instance.range.setStartAfter(node);
              instance.range.collapse(true);
            }
            inserted = true;
          }
        }
      } catch (error) {
        this.debug ? console.error(error) : null;
      }
      if (!inserted) {
        var current = $editor.trumbowyg("html") || "";
        var extra = node.outerHTML || node.textContent || "";
        $editor.trumbowyg("html", current + extra);
      }
      this.content = $editor.trumbowyg("html");
    },
    fetchEmbedList: function (path, listKey) {
      var self = this;
      this.embedLoading[listKey] = true;
      this.embedErrors[listKey] = "";
      $.ajax({
        type: "GET",
        url: BASEURL + path,
        dataType: "json",
        success: function (response) {
          var rows = self.normalizeEmbedRows(response && response.data);
          self.embedLists[listKey] = rows.filter(self.isEmbedPublished);
          self.embedErrors[listKey] = "";
        },
        error: function (xhr) {
          self.debug ? console.log(xhr) : null;
          self.embedLists[listKey] = [];
          self.embedErrors[listKey] =
            xhr && xhr.status === 403 ? "forbidden" : "error";
          self.toast("pages_embed_load_error");
        },
        complete: function () {
          self.embedLoading[listKey] = false;
          self.embedLoaded[listKey] = true;
        },
      });
    },
    loadEmbedTab: function (listKey) {
      if (!listKey || !this.embedEndpoints[listKey]) {
        return;
      }
      if (!this.embedCanShow(listKey)) {
        return;
      }
      if (this.embedLoading[listKey]) {
        return;
      }
      if (this.embedLoaded[listKey] && !this.embedErrors[listKey]) {
        return;
      }
      this.fetchEmbedList(this.embedEndpoints[listKey], listKey);
    },
    onEmbedTabClick: function (listKey) {
      if (listKey === "file") {
        this.openEditorFileModal();
        return;
      }
      this.loadEmbedTab(listKey);
    },
    initEmbedTabs: function () {
      var self = this;
      var tabsEl = document.getElementById("pageEmbedTabs");
      if (!tabsEl) {
        return;
      }
      var existing = M.Tabs.getInstance(tabsEl);
      if (existing && typeof existing.destroy === "function") {
        existing.destroy();
      }
      var firstLink = tabsEl.querySelector(".tab a");
      if (firstLink && !tabsEl.querySelector(".tab a.active")) {
        firstLink.classList.add("active");
      }
      var tabs = M.Tabs.init(tabsEl, {
        onShow: function (tabEl) {
          var id = tabEl && tabEl.id ? tabEl.id : "";
          var key = id.replace("page-embed-", "");
          if (key === "file") {
            self.openEditorFileModal();
            return;
          }
          self.loadEmbedTab(key);
        },
      });
      if (tabs && typeof tabs.updateTabIndicator === "function") {
        tabs.updateTabIndicator();
      }
    },
    openEmbedModal: function () {
      this.saveEditorRange();
      this.loadEmbedTab("form");
      var self = this;
      this.$nextTick(function () {
        var modalEl = document.getElementById("pageEmbedModal");
        if (!modalEl) {
          return;
        }
        var modal = M.Modal.getInstance(modalEl);
        if (!modal) {
          modal = M.Modal.init(modalEl, {});
        }
        modal.open();
        self.$nextTick(function () {
          self.initEmbedTabs();
        });
      });
    },
    closeEmbedModal: function () {
      var modalEl = document.getElementById("pageEmbedModal");
      var modal = modalEl ? M.Modal.getInstance(modalEl) : null;
      if (modal) {
        modal.close();
      }
    },
    openEditorFileModal: function () {
      this.saveEditorRange();
      var el = document.getElementById("editorFileModal");
      if (!el) {
        return;
      }
      var modal = M.Modal.getInstance(el);
      if (!modal) {
        modal = M.Modal.init(el, {});
      }
      modal.open();
    },
    insertEmbedToken: function (helper, name) {
      if (!name) {
        return;
      }
      name = String(name)
        .replace(/\u00a0/g, " ")
        .replace(/\s+/g, " ")
        .trim();
      if (!name) {
        return;
      }
      var token = "{{" + helper + "(" + name + ")}}";
      this.insertEditorNode(document.createTextNode(token));
      this.closeEmbedModal();
    },
    addCustomMeta() {
      this.customMetas.push({
        name: "",
        content: "",
      });
    },
    removeMeta(index, is_custom = true) {
      if (is_custom) {
        this.customMetas.splice(index, 1);
      } else {
        this.metas.splice(index, 1);
      }
    },
    getMeta(strProperty) {
      let result_meta = false;
      this.metas.forEach((meta) => {
        if (meta.property == strProperty || meta.name == strProperty) {
          result_meta = meta;
        }
      });
      return result_meta;
    },
    setMetaContent(strValue, strProperty, index) {
      var div = document.createElement("div");
      div.innerHTML = strValue;
      var text = div.textContent || div.innerText || "";
      if (text.length > 370) text = text.substring(0, 370) + "...";
      if (index !== undefined) {
        this.metas[index].content = text;
        return;
      }
      this.metas = this.metas.map((meta) => {
        if (meta.property == strProperty || meta.name == strProperty) {
          meta.content = text;
        }
        return meta;
      });
    },
    getPathSegments() {
      /**
       * url path:
       * pageType / Categorie / SubCategorie / pagePath
       */
      let type = "";
      if (this.page_type_id == 1) {
        type = "";
      } else {
        type = this.getSelectedPageType();
        if (type.length) {
          type = type[0].page_type_name;
        } else {
          type = "";
        }
      }

      let categorie = this.getSelectedCategorie();
      categorie = categorie[0] ? categorie[0]["name"] : "";
      categorie = type == "" ? "" : categorie;
      let subcategorie = this.getSelectedSubCategorie();
      subcategorie = subcategorie[0] ? subcategorie[0]["name"] : "";
      subcategorie = type == "" ? "" : subcategorie;
      let pagePath = this.path;
      if (pagePath.indexOf("blog/") !== -1) {
        pagePath = pagePath.split("blog/")[1];
      }
      if (pagePath.indexOf("news/") !== -1) {
        pagePath = pagePath.split("news/")[1];
      }
      return [type, categorie, subcategorie, pagePath];
    },
    onChangeTitle(title) {
      this.page_data.title = title;
      this.setMetaContent(title, "og:title");
      this.setMetaContent(title, "twitter:title");
      this.setMetaContent(title, "keywords");
    },
    autoSave() {
      // Llama a la versión debounced (se ejecutará solo después de 2s sin cambios)
      this.debouncedAutoSave();
    },
    removeImage(index) {
      if (this.mainImage.length > 0) {
        this.mainImage.splice(index, 1);
      }
      if (this.mainImage.length == 0) {
        this.mainImage = [];
        this.setMetaContent("", "og:image");
        this.setMetaContent("", "twitter:image");
      }
    },
    getFileImagenPath(file) {
      return BASEURL + file.file_path.substr(2) + this.getFileImagenName(file);
    },
    getFileImagenName(file) {
      return file.file_name + "." + file.file_type;
    },
    setPath(value) {
      let slug = this.string_to_slug(value);
      this.path = slug;
      this.setMetaContent(BASEURL + slug, "og:url");
    },
    string_to_slug: function (str) {
      if (str.length == 0) return "";

      str = str.replace(/^\s+|\s+$/g, ""); // trim

      str = str.toLowerCase();

      // remove accents, swap ñ for n, etc
      var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
      var to = "aaaaeeeeiiiioooouuuunc-/----";
      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
      }

      str = str
        .replace(/[^a-z0-9 -/]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

      return str;
    },
    validateField(field) {
      if (this.form.validateField(field)) {
        this.serverValidation(field);
        return;
      }
      return this.form.fields[field].valid;
    },
    validateForm() {
      this.form.validate();
      var scheduleOk =
        this.publishondate ||
        !this.status ||
        (!!this.datepublish && !!this.timepublish);
      return this.form.errors.length == 0 && scheduleOk;
    },
    pageSaveErrorToast: function (xhr) {
      var json = xhr && xhr.responseJSON ? xhr.responseJSON : {};
      var msg = json.error_message || "";
      if (json.errors && json.errors.path) {
        msg = msg ? msg + " " + json.errors.path : json.errors.path;
      }
      if (msg) {
        M.toast({ html: msg });
        return;
      }
      this.toast("toast_error");
    },
    save() {
      var self = this;
      var callBack = function (response) {
        var toastHTML = "";
        if (response.data.status == 1) {
          toastHTML =
            "<span>" +
            self.lang("toast_saved") +
            '</span><a target="_blank" href="' +
            BASEURL +
            response.data.path +
            '" class="btn-flat toast-action">' +
            self.lang("pages_view_in_site") +
            "</a>";
        } else {
          toastHTML =
            "<span>" +
            self.lang("toast_saved") +
            '</span><a target="_blank" href="' +
            BASEURL +
            "admin/pages/preview?page_id=" +
            response.data.page_id +
            '" class="btn-flat toast-action">' +
            self.lang("pages_preview") +
            "</a>";
        }
        M.toast({ html: toastHTML });
      };
      if (self.validateForm()) {
        this.saving = true;
        this.runSaveData(callBack);
      } else {
        this.toast("toast_form_invalid");
      }
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/pages";
      $.ajax({
        type: "POST",
        url: url,
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          self.saving = false;
          if (response.code == 200) {
            self.editMode = true;
            self.page_id = response.data.page_id;
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: response.error_message });
          }
        },
        error: function (xhr) {
          self.saving = false;
          self.pageSaveErrorToast(xhr);
          self.debug ? console.error(xhr) : null;
        },
      });
    },
    getData: function () {
      let content = this.content;

      var span = document.createElement("span");
      span.innerHTML = content;
      let text = span.textContent || span.innerText;

      let meta = this.getMeta("description");
      if ((meta && meta.content == "") || meta.content == "...") {
        this.setMetaContent(text, "description");
      }
      meta = this.getMeta("og:description");
      if ((meta && meta.content == "") || meta.content == "...") {
        this.setMetaContent(text, "og:description");
      }
      meta = this.getMeta("twitter:description");
      if ((meta && meta.content == "") || meta.content == "...") {
        this.setMetaContent(text, "twitter:description");
      }
      return {
        title: this.form.fields.title.value || "",
        subtitle: this.form.fields.subtitle.value || "",
        path: this.getPagePath || "",
        page_type_id: this.page_type_id || 1,
        status: this.status ? 1 : 2,
        content: this.content,
        json_content: JSON.stringify(this.json_content),
        page_id: this.page_id || null,
        publishondate: this.publishondate,
        date_publish: this.getDateTimePublish,
        visibility: this.visibility,
        template: this.template || "default",
        layout: this.layout || "default",
        categorie_id: this.categorie_id || 0,
        subcategorie_id: this.subcategorie_id || 0,
        mainImage: this.getMainImagenPath,
        thumbnailImage: this.getThumbnailImagePath,
        page_data: {
          tags: this.getPageTags(),
          title: this.page_data.title,
          footer_includes: this.page_data.footer_includes,
          headers_includes: this.page_data.headers_includes,
          meta: [...this.metas, ...this.customMetas],
        },
      };
    },
    getPageTags() {
      let tags = [];
      const chipsEl = document.getElementById("pageTags");
      const instance = chipsEl ? M.Chips.getInstance(chipsEl) : null;
      if (!instance || !instance.chipsData) {
        return tags;
      }
      instance.chipsData.forEach((element) => {
        tags.push(element.tag);
      });
      return tags;
    },
    getTemplates() {
      var self = this;
      var url = BASEURL + "api/v1/pages/templates";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.templates = response.data.templates.map(function (value) {
              let template = value.split(".blade")[0];
              return template == "template" ? "default" : template;
            });
            self.layouts = response.data.layouts.map(function (value) {
              let layout = value.split(".")[0];
              return layout == "site" ? "default" : layout;
            });
            self.finishBootRequest(true);
          } else {
            self.finishBootRequest(false);
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.finishBootRequest(false);
        },
      });
    },
    getSelectedPageType() {
      return this.pageTypes.filter((value, index) => {
        return this.page_type_id == value.page_type_id;
      });
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.categorie_id == value.categorie_id;
      });
    },
    getSelectedSubCategorie() {
      return this.subcategories.filter((value, index) => {
        return this.subcategorie_id == value.categorie_id;
      });
    },
    serverValidation(field) {
      var self = this;
      var url = BASEURL + "admin/users/ajax_check_field";
      $.ajax({
        type: "POST",
        url: url,
        data: {
          field: field,
          value: self.form.fields[field].value,
        },
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code) {
            self.form.fields[field].valid = response.data;
            if (response.data) {
              self.form.markFieldAsValid(field);
            } else {
              self.form.fields[field].errorText =
                "The " + field + " is already registered";
            }
            self.$forceUpdate();
          }
        },
      });
    },
    getPageTypes() {
      var self = this;
      var url = BASEURL + "api/v1/pages/types";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.pageTypes = response.data;
            self.finishBootRequest(true);
          } else {
            self.finishBootRequest(false);
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.finishBootRequest(false);
        },
      });
    },
    getCategories() {
      var self = this;
      var url = BASEURL + "api/v1/categories/type/page";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.categories = response.data;
            self.finishBootRequest(true);
          } else {
            self.finishBootRequest(false);
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.finishBootRequest(false);
        },
      });
    },
    getSubCategories() {
      var self = this;
      var url = BASEURL + "api/v1/categories/subcategorie/" + self.categorie_id;
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.subcategories = response.data;
            PageNewForm.initSelects();
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
        },
      });
    },
    checkEditMode() {
      var page_id = document.getElementById("page_id").value;
      var editMode = document.getElementById("editMode").value;
      localStorage.removeItem("g-editor-page");
      if (page_id && editMode == "edit") {
        var self = this;
        self.editMode = true;
        self.beginBoot(2);
        self.getCategories();
        var url = BASEURL + "api/v1/pages/editpageinfo/" + page_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.form.fields.title.value = response.data.page.title;
              self.form.fields.subtitle.value = response.data.page.subtitle;
              self.page_id = response.data.page.page_id;
              self.status = response.data.page.status == "1";
              self.path = response.data.page.path;
              self.visibility = response.data.page.visibility;
              var datePublish = response.data.page.date_publish;
              var hasSchedule = !!(
                datePublish && String(datePublish).indexOf("0000-00-00") === -1
              );
              self.publishondate = !hasSchedule;
              if (hasSchedule) {
                var dateParts = String(datePublish).split(" ");
                self.datepublish = dateParts[0] || "";
                var timePart = dateParts[1] || "";
                self.timepublish =
                  timePart.length >= 5 ? timePart.substring(0, 5) : timePart;
              }
              self.template = response.data.page.template;
              self.categorie_id = response.data.page.categorie_id || 0;
              self.subcategories_id = response.data.page.subcategories_id || 0;
              self.pageTypes = response.data.page_types;
              self.page_type_id = response.data.page.page_type_id;
              self.page_data = response.data.page.page_data;
              const chipsEl = document.getElementById("pageTags");
              const instance = chipsEl ? M.Chips.getInstance(chipsEl) : null;
              response.data.page.page_data.tags && instance
                ? response.data.page.page_data.tags.forEach((element) => {
                    self.debug ? console.log({ element }) : null;
                    instance.addChip({
                      tag: element,
                    });
                  })
                : null;
              response.data.page.page_data.meta
                ? (self.metas = response.data.page.page_data.meta)
                : null;
              self.user = new User(response.data.page.user);
              if (response.data.page.main_image) {
                self.mainImage.push(response.data.page.main_image);
              }
              if (response.data.page.thumbnail_image) {
                self.mainImage.push(response.data.page.thumbnail_image);
              }
              self.templates = response.data.templates.map(function (value) {
                let template = value.split(".blade")[0];
                return template == "template" ? "default" : template;
              });
              self.layouts = response.data.layouts.map(function (value) {
                let layout = value.split(".")[0];
                return layout == "site" ? "default" : layout;
              });
              self.json_content = response.data.page.json_content;
              self.content = response.data.page.content;
              self.finishBootRequest(true);
            } else {
              self.finishBootRequest(false);
            }
          })
          .catch((response) => {
            self.debug ? console.log(response) : null;
            self.finishBootRequest(false);
          });
      } else {
        this.beginBoot(3);
        this.getPageTypes();
        this.getTemplates();
        this.getCategories();
      }
    },
    copyCallcack(files) {
      files = files.map((file) => new ExplorerFile(file));
      if (this.modalCallbackMode == "copyCallcack") {
        let file = files[0];
        this.mainImage = [...this.mainImage, ...files];
        if (this.mainImage.length > 2) {
          this.mainImage = this.mainImage.slice(0, 2);
        }
        this.setMetaContent(file.get_relative_file_path(), "og:image");
        this.setMetaContent(file.get_relative_file_path(), "twitter:image");
        let instance = M.Modal.getInstance($("#fileUploader"));
        instance.close();
        this.initMaterialboxed();
      } else {
        let container = document.getElementById(this.modalCallbackTargetID);
        let img = container.querySelector("img");

        let file = files[0];
        let url = file.get_full_file_path();
        img.setAttribute("src", url);

        let instance = M.Modal.getInstance($("#fileUploader"));
        instance.close();
        this.initMaterialboxed();
      }
    },
    onSelectImageCallcack(files) {
      files = files.map((file) => new ExplorerFile(file));
      this.debug ? console.log(files) : null;
      let instance = M.Modal.getInstance($("#editorModal"));
      if (instance) {
        instance.close();
      }
      var self = this;
      files.forEach((file) => {
        var node = $(
          '<img alt="' +
            file.file_name +
            '" src="' +
            file.get_full_file_path() +
            '" />'
        )[0];
        self.insertEditorNode(node);
      });
    },
    onSelectEditorFileCallback(files) {
      files = files.map((file) => new ExplorerFile(file));
      var fileModalEl = document.getElementById("editorFileModal");
      var fileModal = fileModalEl ? M.Modal.getInstance(fileModalEl) : null;
      if (fileModal) {
        fileModal.close();
      }
      this.closeEmbedModal();
      var self = this;
      files.forEach((file) => {
        var node;
        if (self.isEmbedImageFile(file)) {
          node = $(
            '<img alt="' +
              file.file_name +
              '" src="' +
              file.get_full_file_path() +
              '" />'
          )[0];
        } else {
          node = $(
            '<a href="' +
              file.get_full_file_path() +
              '" target="_blank" rel="noopener">' +
              file.get_filename() +
              "</a>"
          )[0];
        }
        self.insertEditorNode(node);
      });
    },
    initEditor: function () {
      var self = this;
      $("#editor")
        .trumbowyg({
          semantic: false,
          btns: [
            ["viewHTML"],
            ["formatting"],
            ["strong", "em", "del"],
            ["superscript", "subscript"],
            ["link"],
            ["insertImage"],
            ["uploadimage"],
            ["justifyLeft", "justifyCenter", "justifyRight", "justifyFull"],
            ["unorderedList", "orderedList"],
            ["horizontalRule"],
            ["removeformat"],
            ["fullscreen"],
          ],
        })
        .on("tbwchange", function () {
          self.content = $("#editor").trumbowyg("html");
        })
        .on("tbwblur", function () {
          self.saveEditorRange();
        });
      trumbowygInstance = $("#editor").data("trumbowyg");
      if (self.content) {
        $("#editor").trumbowyg("html", self.content);
      }
    },
    initPlugins() {
      var formTabsEl = document.getElementById("formTabs");
      if (formTabsEl) {
        var existingTabs = M.Tabs.getInstance(formTabsEl);
        if (existingTabs && typeof existingTabs.destroy === "function") {
          existingTabs.destroy();
        }
        var formTabs = M.Tabs.init(formTabsEl, {});
        if (formTabs && typeof formTabs.updateTabIndicator === "function") {
          formTabs.updateTabIndicator();
        }
      }
      var elems = document.getElementById("pageMetas");
      if (elems) {
        M.Collapsible.init(elems, {
          accordion: false,
        });
      }
      var dateEls = document.querySelectorAll(".datepicker");
      M.Datepicker.init(dateEls, {
        format: "yyyy-mm-dd",
        onClose: function () {
          PageNewForm.datepublish =
            document.getElementById("datepublish").value;
        },
      });
      var timeEls = document.querySelectorAll(".timepicker");
      M.Timepicker.init(timeEls, {
        twelveHour: false,
        defaultTime: "now",
        onCloseEnd: function () {
          PageNewForm.timepublish =
            document.getElementById("timepublish").value;
        },
      });
      this.initSelects();
      var embedModal = document.getElementById("pageEmbedModal");
      if (embedModal && !M.Modal.getInstance(embedModal)) {
        M.Modal.init(embedModal, {});
      }
    },
    initMaterialboxed() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
      }, 500);
    },
    initSelects() {
      var self = this;
      this.$nextTick(function () {
        var elems = document.querySelectorAll("#PageNewForm-root select");
        M.FormSelect.init(elems, {});
        self.initMaterialboxed();
      });
    },
    setEditorContent: function (page) {
      return;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted PageNewForm") : null;

      M.Chips.init(document.getElementById("pageTags"), {
        placeholder: "Enter a value",
      });

      setTimeout(() => {
        const instance = M.Chips.getInstance(
          document.getElementById("pageTags")
        );
        if (!instance) {
          return;
        }
        instance.options.onChipAdd = (value) => {
          this.page_data.tags = this.getPageTags();
        };
        instance.options.onChipDelete = (value) => {
          this.page_data.tags = this.getPageTags();
        };
      }, 2000);

      this.checkEditMode();
    });
  },
});
