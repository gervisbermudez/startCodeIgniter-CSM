function configT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

var DataList = new Vue({
  el: "#root",
  data: {
    sectionActive: "backups",
    routes: ["backups", "import", "export"],
    files: [],
    searchQuery: "",
    creatingBackup: false,
    fileToDelete: null,
    exportData: {
      pages: [],
      config: [],
    },
    loader: false,
    selectedFile: false,
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      return true;
    },
    selectedData: function () {
      return {
        pages: this.exportData.pages
          .filter(function (item) {
            return item.checked;
          })
          .map(function (item) {
            return item.page_id;
          }),
        config: this.exportData.config
          .filter(function (item) {
            return item.checked;
          })
          .map(function (item) {
            return item.site_config_id;
          }),
      };
    },
    filteredFiles: function () {
      if (!this.searchQuery) {
        return this.files;
      }
      var query = this.searchQuery.toLowerCase();
      return this.files.filter(function (file) {
        return (
          file.get_filename().toLowerCase().indexOf(query) !== -1 ||
          file.file_path.toLowerCase().indexOf(query) !== -1
        );
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
        M.Collapsible.init(e, {});
        e = document.querySelectorAll("select");
        M.FormSelect.init(e, {});
        e = document.querySelectorAll(".modal");
        M.Modal.init(e, {});
      });
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    reloadFileExplorer: function () {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/reload_file_explorer",
        data: { path: "./backups/database/" },
        dataType: "json",
        success: function () {
          self.getDatabaseBackups();
        },
      });
    },
    getDatabaseBackups: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: { path: "./backups/database/" },
        dataType: "json",
        success: function (t) {
          if (200 == t.code && t.data.length) {
            self.files = t.data.map(function (item) {
              return new ExplorerFile(item);
            });
            self.initPlugins();
          } else {
            self.files = [];
          }
        },
      });
    },
    deleteFile: function (file) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/delete/" + file.file_id,
        data: { file: file },
        dataType: "json",
        success: function (e) {
          if (200 == e.code) {
            M.toast({ html: configT("backupDeleted") });
            self.getDatabaseBackups();
            var modal = M.Modal.getInstance(
              document.getElementById("deleteBackupModal")
            );
            if (modal) {
              modal.close();
            }
          }
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
            self.reloadFileExplorer();
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
              if (fileContent["pages"]) {
                self.exportData.pages = fileContent["pages"];
              }
              if (fileContent["config"]) {
                self.exportData.config = fileContent["config"];
              }
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
            M.toast({ html: configT("error") });
          }
          self.loader = false;
        },
        error: function () {
          self.loader = false;
          M.toast({ html: configT("error") });
        },
      });
    },
    getData: function () {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/export_data",
        data: {},
        dataType: "json",
        success: function (response) {
          self.exportData = response.data;
          self.exportData.pages = response.data.pages.map(function (page) {
            return Object.assign({}, page, {
              checked: false,
              user: new User(page.user),
            });
          });
          self.exportData.config = response.data.config.map(function (item) {
            var parsed = {};
            try {
              parsed = JSON.parse(item.config_data);
            } catch (err) {
              parsed = {};
            }
            return Object.assign({}, item, {
              checked: false,
              user: new User(item.user),
              config_data: parsed,
            });
          });
          self.loader = false;
          self.initPlugins();
        },
        error: function () {
          self.loader = false;
          M.toast({ html: configT("error") });
        },
      });
    },
    generateFile: function () {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/generate_export_file",
        data: { exportData: this.selectedData },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: configT("exportOk") });
            self.download_export_file(response.data.exportFilename);
          } else {
            self.loader = false;
            M.toast({ html: configT("error") });
          }
        },
        error: function () {
          self.loader = false;
          M.toast({ html: configT("error") });
        },
      });
    },
    toggleData: function (items, itemsName) {
      this.exportData[itemsName] = items.map(function (item) {
        return Object.assign({}, item, { checked: !item.checked });
      });
    },
    download_export_file: function (fileName) {
      var self = this;
      fetch(BASEURL + "temp/" + fileName)
        .then(function (resp) {
          return resp.blob();
        })
        .then(function (blob) {
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement("a");
          a.style.display = "none";
          a.href = url;
          a.download = fileName;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          M.toast({ html: configT("downloadStarted") });
          self.loader = false;
        })
        .catch(function () {
          M.toast({ html: configT("error") });
          self.loader = false;
        });
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
