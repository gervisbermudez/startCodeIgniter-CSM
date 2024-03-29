var ConfiguracionList = new Vue({
  el: "#root",
  data: {
    configurations: [],
    tableView: !1,
    loader: !0,
    filter: "",
    sectionActive: "home",
    routes: [
      "home",
      "analytics",
      "seo",
      "pixel",
      "database",
      "theme",
      "updater",
      "addConfig",
    ],
    files: [],
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
  },
  mixins: [mixins],
  computed: {
    seoConfigurations: function () {
      if (this.filter) {
        let e = this.filter.toLowerCase();
        return this.configurations.filter(
          (t, a) => this.searchInObject(t, e) && "seo" == t.config_type
        );
      }
      return this.filterConfigurations("seo");
    },
    generalConfigurations: function () {
      return this.filterConfigurations("general");
    },
    analyticsConfigurations: function () {
      return this.filterConfigurations("analytics");
    },
    seoConfigurations: function () {
      return this.filterConfigurations("seo");
    },
    pixelConfigurations: function () {
      return this.filterConfigurations("pixel");
    },
    themeConfigurations: function () {
      return this.filterConfigurations("theme");
    },
    updaterConfig: function () {
      return this.filterConfigurations("updater");
    },
    loggerConfig: function () {
      return this.filterConfigurations("logger");
    },
  },
  methods: {
    saveNewConfig: function () {
      var payload = this.newConfig;
      payload['config_data'] = JSON.stringify(payload.config_data);

      var data = new FormData();
      for (var key in payload) {
        data.append(key, payload[key]);
      }

      fetch(BASEURL + "/api/v1/config/",
        {
          method: "POST",
          body: data
        })
        .then((res) => { return res.json(); })
        .then((response) => {
          console.log(response);
          if (response.code == 200) {
            this.configurations.push(response.data);
            M.toast({
              html: "Config saved!",
            });
            this.newConfig.config_name = "";
            this.newConfig.config_value = "";
            this.newConfig.config_description = "";
          } else {
            console.log(response),
              M.toast({
                html: "an unexpected error has occurred",
              });
          }
        }).catch(error => {
          console.error(error),
            M.toast({
              html: "an unexpected error has occurred",
            });
        })
    },
    getConfig: function (e, t = "config_name") {
      let a = null;
      return (
        this.configurations.forEach((o) => {
          o[t] == e && (a = o);
        }),
        a
      );
    },
    getConfigValue: function (e, t = "config_name") {
      let a = this.getConfig(e, t);
      return a ? a.config_value : "";
    },
    getConfigValueBoolean: function (e, t = "config_name") {
      let a = this.getConfig(e, t);
      return a && a.config_value == a.config_data.true;
    },
    updateConfig(e, t = "config_name") {
      (value = e.target.value),
        (config = this.getConfig(t)),
        (config.config_value = value),
        this.runSave(config);
    },
    updateConfigCheckbox(e, t = "ANALYTICS_ACTIVE") {
      (value = e.target.checked),
        (config = this.getConfig(t)),
        value
          ? config.config_data.perm_values.forEach((e) => {
            e == config.config_data.true && (value = e);
          })
          : config.config_data.perm_values.forEach((e) => {
            e != config.config_data.true && (value = e);
          }),
        (config.config_value = value),
        this.runSave(config);
    },
    filterConfigurations: function (e) {
      return this.configurations.filter((t, a) => t.config_type == e);
    },
    changeSectionActive: function (e) {
      switch (((this.sectionActive = e), e)) {
        case "theme":
          this.getThemes();
          break;
        case "database":
          this.getDatabaseBackups();
          break;
        case "updater":
          break;
      }
      var t =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?section=" +
        e;
      window.history.pushState(
        {
          path: t,
        },
        "",
        t
      );
    },
    checkUpdates() {
      (this.updaterloader = !0),
        fetch(BASEURL + "api/v1/config/check_update")
          .then((e) => e.json())
          .then((e) => {
            console.log(e),
              (this.updaterloader = !1),
              (this.updaterInfo = e.data);
          })
          .catch((e) => {
            (this.updaterloader = !1),
              console.log(e),
              M.toast({
                html: "an unexpected error has occurred",
              });
          });
    },
    downloadUpdateVersion() {
      (this.updaterProgress = !0),
        fetch(BASEURL + "api/v1/config/download_update")
          .then((e) => e.json())
          .then((e) => {
            200 == e.code
              ? ((this.updaterProgress = !1),
                (this.updaterPackageDownloadedName = e.data.downloaded_file),
                (this.updaterPackageDownloaded = !0),
                M.toast({
                  html: e.data.message,
                }))
              : M.toast({
                html: e.data.message,
              });
          })
          .catch((e) => {
            (this.updaterProgress = !1),
              console.log(e),
              M.toast({
                html: "an unexpected error has occurred",
              });
          });
    },
    installDownloadedPackage() {
      (this.updaterInstallProgress = !0),
        fetch(
          BASEURL +
          "api/v1/config/install_downloaded_update?packagename=" +
          this.updaterPackageDownloadedName
        )
          .then((e) => e.json())
          .then((e) => {
            200 == e.code
              ? ((this.updaterInstallProgress = !1),
                M.toast({
                  html: e.data.message,
                }))
              : M.toast({
                html: e.data.message,
              });
          })
          .catch((e) => {
            (this.updaterInstallProgress = !1),
              console.log(e),
              M.toast({
                html: "an unexpected error has occurred",
              });
          });
    },
    getThemes() {
      fetch(BASEURL + "api/v1/config/themes")
        .then((e) => e.json())
        .then((e) => {
          this.themes = e.data;
        })
        .catch((e) => {
          console.log(e),
            M.toast({
              html: "an unexpected error has occurred",
            });
        });
    },
    getThemePreviewUrl: (e, t) =>
      t.preview
        ? BASEURL + "themes/" + e + "/" + t.preview
        : BASEURL + "public/img/profile/default.png",
    changeTheme(e) {
      let t = this.getConfig("THEME_PATH");
      (t.config_value = e), this.saveConfig(t);
    },
    getThemeIsChecked(e) {
      return this.getConfig("THEME_PATH").config_value == e;
    },
    toggleView: function () {
      (this.tableView = !this.tableView), this.initPlugins();
    },
    toggleEddit(e) {
      (e.editable = !e.editable), this.$forceUpdate();
    },
    resetFilter: function () {
      this.filter = "";
    },
    initPlugins: function () {
      setTimeout(() => {
        var e = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(e, {});
        e = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(e, {});
        e = document.querySelectorAll(".collapsible");
        M.Collapsible.init(e, {});
        e = document.querySelectorAll("select");
        M.FormSelect.init(e, {});
      }, 3e3);
    },
    saveConfig(e) {
      if ((this.toggleEddit(e), "boolean" != e.config_data)) {
        let t = new VueForm({
          field: {
            value: e.config_value,
            required: !0,
            type: e.config_data.validate_as,
            maxLength: e.config_data.max_lenght,
            minLength: e.config_data.min_lenght,
          },
        });
        t.validate(),
          t.errors.length > 0
            ? ((e.validate = !1),
              M.toast({
                html: "Verificar la configuracion del campo",
              }))
            : ((e.validate = !0), this.runSave(e));
      } else this.runSave(e);
    },
    runSave(e) {
      var t = this,
        a = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: a,
        data: e,
        dataType: "json",
        success: function (e) {
          t.debug && console.log(a, e),
            200 == e.code
              ? ("function" == typeof callBack && callBack(e),
                M.toast({
                  html: "Config Saved!",
                }))
              : M.toast({
                html: e.responseJSON.error_message,
              });
        },
        error: function (e) {
          M.toast({
            html: e.responseJSON.error_message,
          });
        },
      });
    },
    getconfigurations: function () {
      var e = this,
        t = BASEURL + "api/v1/config/";
      fetch(t)
        .then((e) => e.json())
        .then((t) => {
          let a = t.data;
          for (const e in a)
            if (a.hasOwnProperty(e)) {
              a[e].user = new User(a[e].user);
              try {
                a[e].config_data = JSON.parse(a[e].config_data);
              } catch (t) {
                a[e].config_data = {};
              }
            }
          (e.configurations = a), (e.loader = !1);
        })
        .catch((t) => {
          M.toast({
            html: t.responseJSON.error_message,
          }),
            (e.loader = !1);
        });
    },
    deleteConfiguration: function (e, t) {
      var a = this;
      (a.loader = !0),
        $.ajax({
          type: "DELETE",
          url: BASEURL + "api/v1/configuration/" + e.configuration_id,
          data: {},
          dataType: "json",
          success: function (e) {
            200 == e.code && a.configurations.splice(t, 1);
          },
          error: function (e) {
            M.toast({
              html: response.error_message,
            }),
              (a.loader = !1);
          },
        });
    },
    base_url: function (e) {
      return BASEURL + e;
    },
    reloadFileExplorer() {
      var e = this;
      e.files = [];
      var t = BASEURL + "api/v1/files/reload_file_explorer";
      $.ajax({
        type: "POST",
        url: t,
        data: {
          path: "./backups/database/",
        },
        dataType: "json",
        success: function (t) {
          e.getDatabaseBackups();
        },
      });
    },
    getDatabaseBackups() {
      var e = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: {
          path: "./backups/database/",
        },
        dataType: "json",
        success: function (t) {
          (e.fileloader = !1),
            200 == t.code && t.data.length
              ? ((e.files = t.data.map((e) => new ExplorerFile(e))),
                e.initPlugins())
              : (e.files = []);
        },
      });
    },
    deleteFile(e) {
      var t = this;
      e = e;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/delete/" + e.file_id,
        data: {
          file: e,
        },
        dataType: "json",
        success: function (e) {
          if (200 == e.code) {
            (html = "<span>Done! </span>"),
              M.toast({
                html: html,
              }),
              t.getDatabaseBackups();
            var a = document.querySelectorAll(".collapsible");
            M.Collapsible.init(a, {});
          }
        },
      });
    },
    createDatabaseBackup() {
      fetch(BASEURL + "api/v1/config/backup_database")
        .then((e) => e.json())
        .then((e) => {
          "200" == e.code
            ? (M.toast({
              html: e.result,
            }),
              this.reloadFileExplorer())
            : M.toast({
              html: e.result,
            });
        })
        .catch((e) => {
          M.toast({
            html: "Error code: 001",
          });
        });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getconfigurations(),
        window.addEventListener(
          "popstate",
          (e) => {
            try {
              var t = location.search.substring(1);
              (t = JSON.parse(
                '{"' + t.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
                function (e, t) {
                  return "" === e ? t : decodeURIComponent(t);
                }
              )).section && this.routes.includes(t.section)
                ? (this.sectionActive = t.section)
                : (this.sectionActive = "home");
            } catch (e) {
              this.sectionActive = "home";
            }
          },
          !1
        );
      try {
        var e = location.search.substring(1);
        (e = JSON.parse(
          '{"' + e.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
          function (e, t) {
            return "" === e ? t : decodeURIComponent(t);
          }
        )).section &&
          this.routes.includes(e.section) &&
          this.changeSectionActive(e.section);
      } catch (e) {
        this.changeSectionActive("home");
      }
    });
  },
});
