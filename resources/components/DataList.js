function configT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

function emptyPickerStore() {
  return {
    pages: [],
    config: [],
    menus: [],
    fragmentos: [],
    categories: [],
    albums: [],
    videos: [],
    events: [],
    collections: [],
    siteforms: [],
  };
}

function emptyPickerSearch() {
  return {
    pages: "",
    config: "",
    menus: "",
    fragmentos: "",
    categories: "",
    albums: "",
    videos: "",
    events: "",
    collections: "",
    siteforms: "",
  };
}

var PICKER_GROUPS = [
  {
    key: "pages",
    idField: "page_id",
    titleField: "title",
    metaField: "path",
    icon: "web",
    labelKey: "groupPages",
  },
  {
    key: "config",
    idField: "site_config_id",
    titleField: "config_label",
    metaField: "config_name",
    icon: "settings",
    labelKey: "groupConfig",
  },
  {
    key: "menus",
    idField: "menu_id",
    titleField: "name",
    metaField: "name",
    icon: "menu",
    labelKey: "groupMenus",
  },
  {
    key: "fragmentos",
    idField: "fragment_id",
    titleField: "name",
    metaField: "type",
    icon: "view_quilt",
    labelKey: "groupFragmentos",
  },
  {
    key: "categories",
    idField: "categorie_id",
    titleField: "name",
    metaField: "type",
    icon: "folder",
    labelKey: "groupCategories",
  },
  {
    key: "albums",
    idField: "album_id",
    titleField: "name",
    metaField: "name",
    icon: "photo_library",
    labelKey: "groupAlbums",
  },
  {
    key: "videos",
    idField: "video_id",
    titleField: "name",
    metaField: "name",
    icon: "videocam",
    labelKey: "groupVideos",
  },
  {
    key: "events",
    idField: "event_id",
    titleField: "name",
    metaField: "slug",
    icon: "event",
    labelKey: "groupEvents",
  },
  {
    key: "collections",
    idField: "custom_model_id",
    titleField: "form_name",
    metaField: "slug",
    icon: "view_module",
    labelKey: "groupCollections",
  },
  {
    key: "siteforms",
    idField: "siteform_id",
    titleField: "name",
    metaField: "name",
    icon: "assignment",
    labelKey: "groupSiteforms",
  },
];

var DataList = new Vue({
  el: "#root",
  data: {
    sectionActive: "backups",
    routes: ["backups", "import", "export"],
    files: [],
    searchQuery: "",
    creatingBackup: false,
    fileToDelete: null,
    catalogData: emptyPickerStore(),
    importData: emptyPickerStore(),
    pickerSearch: emptyPickerSearch(),
    pickerGroups: PICKER_GROUPS,
    includeUnpublishedPages: false,
    loader: false,
    selectedFile: false,
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      var selected = this.selectedData;
      var i;
      for (i = 0; i < PICKER_GROUPS.length; i++) {
        if (selected[PICKER_GROUPS[i].key] && selected[PICKER_GROUPS[i].key].length) {
          return true;
        }
      }
      return false;
    },
    catalogHasItems: function () {
      var i;
      for (i = 0; i < PICKER_GROUPS.length; i++) {
        if ((this.catalogData[PICKER_GROUPS[i].key] || []).length) {
          return true;
        }
      }
      return false;
    },
    selectedData: function () {
      var src =
        this.sectionActive === "import" ? this.importData : this.catalogData;
      var out = {};
      var i;
      for (i = 0; i < PICKER_GROUPS.length; i++) {
        var group = PICKER_GROUPS[i];
        var items = src[group.key] || [];
        out[group.key] = items
          .filter(function (item) {
            return item.checked;
          })
          .map(function (item) {
            return item[group.idField];
          });
      }
      if (this.sectionActive === "export" && this.includeUnpublishedPages) {
        out.unpublished_pages = 1;
      }
      return out;
    },
    filteredFiles: function () {
      if (!this.searchQuery) {
        return this.files;
      }
      var query = this.searchQuery.toLowerCase();
      return this.files.filter(function (file) {
        var name = (file.filename || "").toLowerCase();
        var path = (file.file_path || "").toLowerCase();
        return name.indexOf(query) !== -1 || path.indexOf(query) !== -1;
      });
    },
    lastBackupDate: function () {
      if (!this.files.length) {
        return configT("na");
      }
      var latest = this.files.reduce(function (prev, current) {
        return new Date(prev.date_create) > new Date(current.date_create)
          ? prev
          : current;
      });
      return this.formatDate(latest.date_create);
    },
    totalSize: function () {
      if (!this.files.length) {
        return "0 MB";
      }
      var total = this.files.reduce(function (sum, file) {
        return sum + (file.file_size || 0);
      }, 0);
      return this.formatFileSize(total);
    },
  },
  methods: {
    changeSectionActive: function (section) {
      if (this.routes.indexOf(section) === -1) {
        section = "backups";
      }
      this.sectionActive = section;
      if (section == "backups") {
        this.getDatabaseBackups();
      }
      if (section == "export") {
        this.getData();
      }
      if (section == "import") {
        this.loader = false;
      }
      var url =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?section=" +
        section;
      window.history.pushState({ path: url }, "", url);
      if (typeof markSidenavConfigLeaf === "function") {
        markSidenavConfigLeaf(section, "backups");
      }
      this.initPlugins();
    },
    initPlugins: function () {
      this.$nextTick(function () {
        var e = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(e, {});
        e = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(e, {});
        e = document.querySelectorAll(".collapsible:not(#slide-out)");
        var i;
        for (i = 0; i < e.length; i++) {
          var inst = M.Collapsible.getInstance(e[i]);
          if (inst) {
            inst.destroy();
          }
        }
        M.Collapsible.init(e, { accordion: false });
        e = document.querySelectorAll("select");
        M.FormSelect.init(e, {});
        e = document.querySelectorAll(".modal");
        M.Modal.init(e, {});
      });
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    groupLabel: function (group) {
      return configT(group.labelKey, group.key);
    },
    pickerStoreName: function () {
      return this.sectionActive === "import" ? "importData" : "catalogData";
    },
    groupItems: function (storeName, groupKey) {
      var store = this[storeName] || {};
      return store[groupKey] || [];
    },
    itemSearchText: function (item, group) {
      var parts = [];
      if (item[group.titleField]) {
        parts.push(String(item[group.titleField]));
      }
      if (group.metaField && item[group.metaField]) {
        parts.push(String(item[group.metaField]));
      }
      if (item.path) {
        parts.push(String(item.path));
      }
      if (item.slug) {
        parts.push(String(item.slug));
      }
      if (item.config_name) {
        parts.push(String(item.config_name));
      }
      return parts.join(" ").toLowerCase();
    },
    visibleGroupItems: function (storeName, group) {
      var items = this.groupItems(storeName, group.key);
      var query = (this.pickerSearch[group.key] || "").trim().toLowerCase();
      if (!query) {
        return items;
      }
      var self = this;
      return items.filter(function (item) {
        return self.itemSearchText(item, group).indexOf(query) !== -1;
      });
    },
    groupSelectedCount: function (storeName, groupKey) {
      return this.groupItems(storeName, groupKey).filter(function (item) {
        return item.checked;
      }).length;
    },
    groupAllChecked: function (storeName, group) {
      var items = this.visibleGroupItems(storeName, group);
      if (!items.length) {
        return false;
      }
      return items.every(function (item) {
        return item.checked;
      });
    },
    groupSomeChecked: function (storeName, group) {
      var items = this.visibleGroupItems(storeName, group);
      var n = items.filter(function (item) {
        return item.checked;
      }).length;
      return n > 0 && n < items.length;
    },
    setStoreItemsChecked: function (storeName, checked) {
      var next = Object.assign({}, this[storeName]);
      var i;
      for (i = 0; i < PICKER_GROUPS.length; i++) {
        var key = PICKER_GROUPS[i].key;
        next[key] = (next[key] || []).map(function (item) {
          return Object.assign({}, item, { checked: !!checked });
        });
      }
      this[storeName] = next;
    },
    exportAllItems: function () {
      if (!this.catalogHasItems) {
        M.toast({ html: configT("exportEmpty") });
        return;
      }
      this.setStoreItemsChecked("catalogData", true);
      var self = this;
      this.$nextTick(function () {
        self.generateFile();
      });
    },
    onGroupSelectAll: function (storeName, group, event) {
      var checked = event.target.checked;
      var query = (this.pickerSearch[group.key] || "").trim().toLowerCase();
      var self = this;
      var next = Object.assign({}, this[storeName]);
      next[group.key] = (next[group.key] || []).map(function (item) {
        var visible =
          !query || self.itemSearchText(item, group).indexOf(query) !== -1;
        if (!visible) {
          return item;
        }
        return Object.assign({}, item, { checked: checked });
      });
      this[storeName] = next;
    },
    markChecked: function (rows) {
      if (!rows || !rows.length) {
        return [];
      }
      return rows.map(function (row) {
        return Object.assign({}, row, { checked: false });
      });
    },
    assignStoreFromPayload: function (payload) {
      var store = emptyPickerStore();
      var i;
      for (i = 0; i < PICKER_GROUPS.length; i++) {
        var key = PICKER_GROUPS[i].key;
        store[key] = this.markChecked(
          payload && payload[key] && payload[key].length ? payload[key] : []
        );
      }
      return store;
    },
    showImportGroup: function (group) {
      return this.groupItems("importData", group.key).length > 0;
    },
    getDatabaseBackups: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/backups",
        dataType: "json",
        success: function (t) {
          if (t && (t.code == 200 || t.code === "200") && t.data && t.data.length) {
            self.files = t.data;
            self.initPlugins();
          } else {
            self.files = [];
          }
        },
        error: function () {
          self.files = [];
        },
      });
    },
    deleteFile: function (file) {
      var self = this;
      if (!file || !file.filename) {
        return;
      }
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/backup_delete",
        data: { file: file.filename },
        dataType: "json",
        success: function (e) {
          if (e && (e.code == 200 || e.code === "200")) {
            M.toast({ html: configT("backupDeleted") });
            self.getDatabaseBackups();
            var modal = M.Modal.getInstance(
              document.getElementById("deleteBackupModal")
            );
            if (modal) {
              modal.close();
            }
          } else {
            M.toast({ html: (e && e.error_message) || configT("error") });
          }
        },
        error: function () {
          M.toast({ html: configT("error") });
        },
      });
    },
    confirmDelete: function (file) {
      this.fileToDelete = file;
      var elem = document.getElementById("deleteBackupModal");
      var modal = M.Modal.getInstance(elem);
      if (!modal) {
        modal = M.Modal.init(elem, {});
      }
      modal.open();
    },
    formatDate: function (dateString) {
      if (!dateString) {
        return configT("na");
      }
      var date = new Date(dateString);
      return date.toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    formatFileSize: function (bytes) {
      if (!bytes || bytes === 0) {
        return "0 Bytes";
      }
      var k = 1024;
      var sizes = ["Bytes", "KB", "MB", "GB"];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i];
    },
    createDatabaseBackup: function () {
      var self = this;
      this.creatingBackup = true;
      fetch(BASEURL + "api/v1/config/backup_database")
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          self.creatingBackup = false;
          if ("200" == e.code || 200 == e.code) {
            M.toast({ html: e.result || configT("saved") });
            self.getDatabaseBackups();
          } else {
            M.toast({ html: e.result || configT("backupCreateError") });
          }
        })
        .catch(function () {
          self.creatingBackup = false;
          M.toast({ html: configT("backupCreateError") });
        });
    },
    handleFileSelect: function (evt) {
      var self = this;
      var files = evt.target.files;
      for (var i = 0, f; (f = files[i]); i++) {
        var reader = new FileReader();
        reader.onload = (function () {
          return function (e) {
            try {
              self.selectedFile = true;
              var fileContent = JSON.parse(e.target.result);
              self.importData = self.assignStoreFromPayload(fileContent);
              self.pickerSearch = emptyPickerSearch();
              self.initPlugins();
            } catch (ex) {
              M.toast({ html: configT("error") });
            }
          };
        })(f);
        reader.readAsText(f);
      }
    },
    saveData: function () {
      var self = this;
      if (!this.btnEnable) {
        M.toast({ html: configT("importEmpty") });
        return;
      }
      this.loader = true;
      var formData = new FormData();
      formData.append("exportData", JSON.stringify(this.selectedData));
      formData.append("import_file", $("#files").prop("files")[0]);
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/import_file",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: configT("importOk") });
          } else {
            M.toast({
              html: self.apiErrorMessage(response) || configT("error"),
            });
          }
          self.loader = false;
        },
        error: function (xhr) {
          self.loader = false;
          M.toast({ html: self.xhrErrorMessage(xhr) });
        },
      });
    },
    getData: function () {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/export_data",
        data: {
          unpublished_pages: self.includeUnpublishedPages ? 1 : 0,
        },
        dataType: "json",
        success: function (response) {
          var data = response && response.data ? response.data : {};
          self.catalogData = self.assignStoreFromPayload(data);
          self.loader = false;
          self.initPlugins();
        },
        error: function () {
          self.loader = false;
          self.catalogData = emptyPickerStore();
          M.toast({ html: configT("error") });
        },
      });
    },
    generateFile: function () {
      var self = this;
      if (!this.btnEnable) {
        M.toast({ html: configT("exportEmpty") });
        return;
      }
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/generate_export_file",
        data: { exportData: JSON.stringify(this.selectedData) },
        dataType: "json",
        success: function (response) {
          if (
            response.code == 200 &&
            response.data &&
            response.data.exportJson
          ) {
            M.toast({ html: configT("exportOk") });
            self.download_export_blob(
              response.data.exportJson,
              response.data.filename || "export_data.json"
            );
          } else {
            self.loader = false;
            M.toast({
              html: self.apiErrorMessage(response) || configT("error"),
            });
          }
        },
        error: function (xhr) {
          self.loader = false;
          M.toast({ html: self.xhrErrorMessage(xhr) });
        },
      });
    },
    download_export_blob: function (jsonText, fileName) {
      try {
        var blob = new Blob([jsonText], {
          type: "application/json;charset=utf-8",
        });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.style.display = "none";
        a.href = url;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      } catch (err) {
        M.toast({ html: configT("error") });
      }
      this.loader = false;
      this.initPlugins();
    },
    apiErrorMessage: function (response) {
      if (response && response.error_message) {
        if (typeof response.error_message === "string") {
          return response.error_message;
        }
        if (response.error_message.message) {
          return response.error_message.message;
        }
      }
      return "";
    },
    xhrErrorMessage: function (xhr) {
      try {
        var body = JSON.parse(xhr.responseText);
        return this.apiErrorMessage(body) || configT("error");
      } catch (e) {
        return configT("error");
      }
    },
    readSectionFromUrl: function () {
      try {
        var query = location.search.substring(1);
        if (!query) {
          return "backups";
        }
        var parsed = JSON.parse(
          '{"' + query.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
          function (k, v) {
            return "" === k ? v : decodeURIComponent(v);
          }
        );
        return parsed.section || "backups";
      } catch (e) {
        return "backups";
      }
    },
  },
  mounted: function () {
    var self = this;
    this.$nextTick(function () {
      window.addEventListener("popstate", function () {
        self.changeSectionActive(self.readSectionFromUrl());
      });
      self.changeSectionActive(self.readSectionFromUrl());
      if (typeof bindSamePageSidenavSections === "function") {
        bindSamePageSidenavSections(function (section) {
          self.changeSectionActive(section || "backups");
        });
      }
    });
  },
});
