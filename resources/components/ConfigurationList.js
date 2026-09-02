function configT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

var ConfiguracionList = new Vue({
  el: "#root",
  data: {
    configurations: [],
    loader: true,
    filter: "",
    sectionActive: "home",
    routes: [
      "home",
      "general",
      "theme",
      "seo",
      "integrations",
      "system",
      "updater",
      "addConfig",
    ],
    legacySections: {
      analytics: "integrations",
      pixel: "integrations",
      logger: "system",
    },
    recentBackupPreview: [],
    themes: [],
    updaterloader: false,
    updaterInfo: null,
    updaterProgress: false,
    updaterInstallProgress: false,
    updaterPackageDownloaded: false,
    updaterPackageDownloadedName: "",
    newConfig: {
      site_config_id: "",
      user_id: "",
      config_name: "",
      config_label: "",
      config_value: "",
      config_description: "",
      config_type: "general",
      config_data: {
        type_value: "string",
        validate_as: "text",
        max_lenght: "250",
        min_lenght: "5",
        handle_as: "input",
        input_type: "text",
        perm_values: null,
      },
      readonly: "0",
      date_create: "",
      date_update: "",
      status: "1",
      validate: true,
    },
    last_state: "",
    systemInfo: {},
    lastCleanupResult: null,
  },
  mixins: [mixins],
  computed: {
    seoConfigurations: function () {
      return this.filterConfigurations("seo");
    },
    generalConfigurations: function () {
      return this.filterConfigurations("general");
    },
    listedConfigurations: function () {
      var list =
        this.sectionActive == "seo"
          ? this.seoConfigurations
          : this.generalConfigurations;
      if (!this.filter) {
        return list;
      }
      var term = this.filter.toLowerCase();
      var self = this;
      return list.filter(function (item) {
        return self.searchInObject(item, term);
      });
    },
    isSiteSection: function () {
      return this.sectionActive === "general" || this.sectionActive === "theme" || this.sectionActive === "seo";
    },
    isSystemSection: function () {
      return this.sectionActive === "system" || this.sectionActive === "updater";
    },
    loggerConfig: function () {
      return this.filterConfigurations("logger");
    },
    systemConfigurations: function () {
      return this.configurations.filter(function (c) {
        return c.config_type == "system";
      });
    },
    healthIssues: function () {
      var issues = [];
      if (
        this.getConfigValueBoolean("ANALYTICS_ACTIVE") &&
        !this.getConfigValue("ANALYTICS_ID")
      ) {
        issues.push({
          type: "warning",
          title: configT("healthAnalyticsTitle"),
          message: configT("healthAnalyticsMsg"),
        });
      }
      if (
        this.getConfigValueBoolean("PIXEL_ACTIVE") &&
        !this.getConfigValue("PIXEL_CODE")
      ) {
        issues.push({
          type: "warning",
          title: configT("healthPixelTitle"),
          message: configT("healthPixelMsg"),
        });
      }
      if (!this.getConfigValue("SITE_DESCRIPTION")) {
        issues.push({
          type: "info",
          title: configT("healthSeoTitle"),
          message: configT("healthSeoMsg"),
        });
      }
      var autoCleanup = this.configurations.find(function (c) {
        return c.config_name === "AUTO_CLEANUP_ENABLED";
      });
      if (!autoCleanup || autoCleanup.config_value != "1") {
        issues.push({
          type: "info",
          title: configT("healthCleanupTitle"),
          message: configT("healthCleanupMsg"),
        });
      }
      if (this.lastCleanupResult) {
        var totalDeleted =
          this.lastCleanupResult.system_logs +
          this.lastCleanupResult.api_logs +
          this.lastCleanupResult.user_tracking;
        if (totalDeleted > 0) {
          issues.push({
            type: "success",
            title: configT("healthCleanupDoneTitle"),
            message: configT("healthCleanupDoneMsg"),
          });
        }
      }
      return issues;
    },
    recentActivity: function () {
      return this.configurations
        .filter(function (c) {
          return c.date_update;
        })
        .sort(function (a, b) {
          return new Date(b.date_update) - new Date(a.date_update);
        })
        .slice(0, 5);
    },
  },
  methods: {
    saveNewConfig: function () {
      var self = this;
      var payload = this.newConfig;
      payload["config_data"] = JSON.stringify(payload.config_data);
      var data = new FormData();
      for (var key in payload) {
        data.append(key, payload[key]);
      }
      fetch(BASEURL + "/api/v1/config/", {
        method: "POST",
        body: data,
      })
        .then(function (res) {
          return res.json();
        })
        .then(function (response) {
          if (response.code == 200) {
            self.configurations.push(response.data);
            M.toast({ html: configT("saved") });
            self.newConfig.config_name = "";
            self.newConfig.config_label = "";
            self.newConfig.config_value = "";
            self.newConfig.config_description = "";
            self.changeSectionActive("general");
          } else {
            M.toast({ html: configT("error") });
          }
        })
        .catch(function () {
          M.toast({ html: configT("error") });
        });
    },
    getConfig: function (e, t) {
      t = t || "config_name";
      var found = null;
      this.configurations.forEach(function (o) {
        if (o[t] == e) {
          found = o;
        }
      });
      return found;
    },
    getConfigValue: function (e, t) {
      t = t || "config_name";
      var a = this.getConfig(e, t);
      return a ? a.config_value : "";
    },
    getConfigValueBoolean: function (e, t) {
      t = t || "config_name";
      var a = this.getConfig(e, t);
      return a && a.config_data && a.config_value == a.config_data.true;
    },
    updateConfig: function (e, t) {
      t = t || "config_name";
      var value = e.target.value;
      var config = this.getConfig(t);
      if (!config) {
        return;
      }
      config.config_value = value;
      this.runSave(config);
    },
    updateConfigCheckbox: function (e, t) {
      t = t || "ANALYTICS_ACTIVE";
      var value = e.target.checked;
      var config = this.getConfig(t);
      if (!config || !config.config_data || !config.config_data.perm_values) {
        return;
      }
      config.config_data.perm_values.forEach(function (item) {
        if (value) {
          if (item == config.config_data.true) {
            value = item;
          }
        } else if (item != config.config_data.true) {
          value = item;
        }
      });
      config.config_value = value;
      this.runSave(config);
    },
    filterConfigurations: function (e) {
      return this.configurations.filter(function (t) {
        return t.config_type == e;
      });
    },
    resolveSection: function (section) {
      if (section == "database") {
        window.location.href = BASEURL + "admin/configuration/data";
        return null;
      }
      if (this.legacySections[section]) {
        return this.legacySections[section];
      }
      return this.routes.indexOf(section) !== -1 ? section : "home";
    },
    changeSectionActive: function (e) {
      var section = this.resolveSection(e);
      if (!section) {
        return;
      }
      this.sectionActive = section;
      if (section == "home") {
        this.getSystemInfo();
      }
      if (section == "theme") {
        this.getThemes();
      }
      var t =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?section=" +
        section;
      window.history.pushState({ path: t }, "", t);
      if (typeof markSidenavConfigLeaf === "function") {
        markSidenavConfigLeaf(section, "home");
      }
      this.$nextTick(function () {
        this.initPlugins();
      });
    },
    checkUpdates: function () {
      var self = this;
      this.updaterloader = true;
      fetch(BASEURL + "api/v1/config/check_update")
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          self.updaterloader = false;
          self.updaterInfo = e.data;
        })
        .catch(function () {
          self.updaterloader = false;
          M.toast({ html: configT("error") });
        });
    },
    downloadUpdateVersion: function () {
      var self = this;
      this.updaterProgress = true;
      fetch(BASEURL + "api/v1/config/download_update")
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          if (200 == e.code) {
            self.updaterProgress = false;
            self.updaterPackageDownloadedName = e.data.downloaded_file;
            self.updaterPackageDownloaded = true;
            M.toast({ html: e.data.message });
          } else {
            M.toast({ html: e.data.message });
          }
        })
        .catch(function () {
          self.updaterProgress = false;
          M.toast({ html: configT("error") });
        });
    },
    installDownloadedPackage: function () {
      var self = this;
      this.updaterInstallProgress = true;
      fetch(
        BASEURL +
          "api/v1/config/install_downloaded_update?packagename=" +
          this.updaterPackageDownloadedName
      )
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          self.updaterInstallProgress = false;
          M.toast({ html: e.data.message });
        })
        .catch(function () {
          self.updaterInstallProgress = false;
          M.toast({ html: configT("error") });
        });
    },
    getThemes: function () {
      var self = this;
      fetch(BASEURL + "api/v1/config/themes")
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          self.themes = e.data;
        })
        .catch(function () {
          M.toast({ html: configT("error") });
        });
    },
    getThemePreviewUrl: function (e, t) {
      return t.preview
        ? BASEURL + "themes/" + e + "/" + t.preview
        : BASEURL + "public/img/profile/default.png";
    },
    changeTheme: function (e) {
      var t = this.getConfig("THEME_PATH");
      if (!t) {
        return;
      }
      t.config_value = e;
      this.saveConfig(t);
    },
    getThemeIsChecked: function (e) {
      var conf = this.getConfig("THEME_PATH");
      return conf && conf.config_value == e;
    },
    resetFilter: function () {
      this.filter = "";
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
      });
    },
    focusInput: function (e) {
      this.last_state = JSON.stringify({
        value: e.config_value,
        label: e.config_label,
      });
    },
    saveConfig: function (e) {
      var current_state = JSON.stringify({
        value: e.config_value,
        label: e.config_label,
      });
      if (current_state === this.last_state) {
        return;
      }
      if (e.config_data && e.config_data.type_value != "boolean") {
        var t = new VueForm({
          field: {
            value: e.config_value,
            required: true,
            type: e.config_data.validate_as,
            maxLength: e.config_data.max_lenght,
            minLength: e.config_data.min_lenght,
          },
        });
        t.validate();
        if (t.errors.length > 0) {
          e.validate = false;
          M.toast({ html: configT("fieldInvalid") });
        } else {
          e.validate = true;
          this.runSave(e);
        }
      } else {
        this.runSave(e);
      }
    },
    runSave: function (e) {
      var a = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: a,
        data: e,
        dataType: "json",
        success: function (resp) {
          if (200 == resp.code) {
            M.toast({ html: configT("saved") });
          } else {
            M.toast({ html: configT("error") });
          }
        },
        error: function () {
          M.toast({ html: configT("error") });
        },
      });
    },
    getconfigurations: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/",
        data: {},
        dataType: "json",
        success: function (response) {
          var a = response.data;
          for (var e in a) {
            if (a.hasOwnProperty(e)) {
              a[e].user = new User(a[e].user);
              try {
                a[e].config_data = JSON.parse(a[e].config_data);
              } catch (err) {
                a[e].config_data = {};
              }
            }
          }
          self.configurations = a;
          self.loader = false;
          self.getSystemInfo();
          self.runAutoCleanup();
          self.initPlugins();
        },
        error: function () {
          M.toast({ html: configT("loadError") });
          self.loader = false;
        },
      });
    },
    runAutoCleanup: function () {
      var self = this;
      var enabled = self.configurations.find(function (c) {
        return c.config_name === "AUTO_CLEANUP_ENABLED";
      });
      if (enabled && enabled.config_value == "1") {
        $.ajax({
          type: "POST",
          url: BASEURL + "api/v1/config/cleanup_logs",
          success: function (response) {
            self.lastCleanupResult = response.data;
          },
        });
      }
    },
    base_url: function (e) {
      return BASEURL + e;
    },
    getDatabaseBackups: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/backups",
        dataType: "json",
        success: function (t) {
          if (t && (t.code == 200 || t.code === "200") && t.data && t.data.length) {
            self.recentBackupPreview = t.data.slice(0, 3);
          } else {
            self.recentBackupPreview = [];
          }
        },
        error: function () {
          self.recentBackupPreview = [];
        },
      });
    },
    getSystemInfo: function () {
      var self = this;
      fetch(BASEURL + "api/v1/config/system_info")
        .then(function (e) {
          return e.json();
        })
        .then(function (e) {
          if (e.code == 200) {
            self.systemInfo = e.data;
          }
        });
    },
    readSectionFromUrl: function () {
      try {
        var query = location.search.substring(1);
        if (!query) {
          return "home";
        }
        var parsed = JSON.parse(
          '{"' + query.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
          function (k, v) {
            return "" === k ? v : decodeURIComponent(v);
          }
        );
        return parsed.section || "home";
      } catch (e) {
        return "home";
      }
    },
  },
  mounted: function () {
    var self = this;
    this.$nextTick(function () {
      self.getconfigurations();
      self.getDatabaseBackups();
      window.addEventListener("popstate", function () {
        self.changeSectionActive(self.readSectionFromUrl());
      });
      self.changeSectionActive(self.readSectionFromUrl());
      if (typeof bindSamePageSidenavSections === "function") {
        bindSamePageSidenavSections(function (section) {
          self.changeSectionActive(section || "home");
        });
      }
    });
  },
});
