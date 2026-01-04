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
      "system",
      "addConfig",
    ],
    files: [],
    searchQuery: '',
    creatingBackup: false,
    fileToDelete: null,
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
    charts: {
      configDistribution: null
    }
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
    systemConfigurations: function () {
      return this.configurations.filter(c => c.config_type == 'system');
    },
    healthIssues: function () {
      let issues = [];

      // Check Analytics
      if (this.getConfigValueBoolean('ANALYTICS_ACTIVE') && !this.getConfigValue('ANALYTICS_ID')) {
        issues.push({
          type: 'warning',
          title: 'Analytics incomplete',
          message: 'Tracking is active but Tracking ID is empty.'
        });
      }

      // Check Pixel
      if (this.getConfigValueBoolean('PIXEL_ACTIVE') && !this.getConfigValue('PIXEL_CODE')) {
        issues.push({
          type: 'warning',
          title: 'Facebook Pixel incomplete',
          message: 'Pixel is active but Head Code is empty.'
        });
      }

      // Check SEO
      if (!this.getConfigValue('SITE_DESCRIPTION')) {
        issues.push({
          type: 'info',
          title: 'SEO Opportunity',
          message: 'Site Description is empty. This affects search engine rankings.'
        });
      }
      // Maintenance Check
      var autoCleanup = this.configurations.find(c => c.config_name === 'AUTO_CLEANUP_ENABLED');
      if (!autoCleanup || autoCleanup.config_value != '1') {
        issues.push({
          type: 'info',
          title: 'Mantenimiento Desactivado',
          message: 'La limpieza automática de logs está desactivada. Los registros antiguos podrían llenar la base de datos.'
        });
      }

      if (this.lastCleanupResult) {
        var totalDeleted = this.lastCleanupResult.system_logs + this.lastCleanupResult.api_logs + this.lastCleanupResult.user_tracking;
        if (totalDeleted > 0) {
          issues.push({
            type: 'success',
            title: 'Limpieza Ejecutada',
            message: 'Se han eliminado ' + totalDeleted + ' registros antiguos para optimizar el sistema.'
          });
        }
      }

      return issues;
    },
    recentActivity: function () {
      return this.configurations
        .filter(c => c.date_update)
        .sort((a, b) => new Date(b.date_update) - new Date(a.date_update))
        .slice(0, 5);
    },
    filteredFiles: function () {
      if (!this.searchQuery) return this.files;
      const query = this.searchQuery.toLowerCase();
      return this.files.filter(file =>
        file.get_filename().toLowerCase().includes(query) ||
        file.file_path.toLowerCase().includes(query)
      );
    },
    lastBackupDate: function () {
      if (!this.files.length) return 'N/A';
      const latest = this.files.reduce((prev, current) =>
        new Date(prev.date_create) > new Date(current.date_create) ? prev : current
      );
      return this.formatDate(latest.date_create);
    },
    totalSize: function () {
      if (!this.files.length) return '0 MB';
      const total = this.files.reduce((sum, file) => sum + (file.file_size || 0), 0);
      return this.formatFileSize(total);
    }
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
            this.newConfig.config_label = "";
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
        case "home":
          this.getSystemInfo();
          setTimeout(() => {
            this.initCharts();
          }, 500);
          break;
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
    focusInput: function (e) {
      this.last_state = JSON.stringify({
        value: e.config_value,
        label: e.config_label,
      });
    },
    saveConfig(e) {
      let current_state = JSON.stringify({
        value: e.config_value,
        label: e.config_label,
      });

      if (current_state === this.last_state) {
        if (e.editable) {
          this.toggleEddit(e);
        }
        return;
      }

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
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/",
        data: {},
        dataType: "json",
        success: function (response) {
          let a = response.data;
          for (const e in a)
            if (a.hasOwnProperty(e)) {
              a[e].user = new User(a[e].user);
              try {
                a[e].config_data = JSON.parse(a[e].config_data);
              } catch (t) {
                a[e].config_data = {};
              }
            }
          self.configurations = a;
          self.loader = !1;
          self.getSystemInfo();
          setTimeout(() => {
            self.initCharts();
          }, 500);
          self.runAutoCleanup();
        },
        error: function (error) {
          M.toast({ html: "Error loading configurations" });
          self.loader = !1;
        }
      });
    },
    runAutoCleanup: function () {
      var self = this;
      // Solo si está habilitada la limpieza automática
      var enabled = self.configurations.find(c => c.config_name === 'AUTO_CLEANUP_ENABLED');
      if (enabled && enabled.config_value == '1') {
        $.ajax({
          type: "POST",
          url: BASEURL + "api/v1/config/cleanup_logs",
          success: function (response) {
            self.lastCleanupResult = response.data;
            if (response.data.system_logs > 0 || response.data.api_logs > 0 || response.data.user_tracking > 0) {
              console.log("Mantenimiento completado", response.data);
            }
          }
        });
      }
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
            M.toast({
              html: '<i class="material-icons left">check_circle</i>Backup eliminado correctamente',
              classes: 'green'
            });
            t.getDatabaseBackups();
            // Close modal
            var modal = M.Modal.getInstance(document.getElementById('deleteBackupModal'));
            if (modal) modal.close();
          }
        },
      });
    },
    confirmDelete(file) {
      this.fileToDelete = file;
      var modal = M.Modal.getInstance(document.getElementById('deleteBackupModal'));
      if (!modal) {
        var elem = document.getElementById('deleteBackupModal');
        modal = M.Modal.init(elem, {});
      }
      modal.open();
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return date.toLocaleDateString('es-ES', options);
    },
    formatFileSize(bytes) {
      if (!bytes || bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    },
    createDatabaseBackup() {
      this.creatingBackup = true;
      fetch(BASEURL + "api/v1/config/backup_database")
        .then((e) => e.json())
        .then((e) => {
          this.creatingBackup = false;
          if ("200" == e.code) {
            M.toast({
              html: '<i class="material-icons left">check_circle</i>' + e.result,
              classes: 'green'
            });
            this.reloadFileExplorer();
          } else {
            M.toast({
              html: '<i class="material-icons left">error</i>' + e.result,
              classes: 'red'
            });
          }
        })
        .catch((e) => {
          this.creatingBackup = false;
          M.toast({
            html: '<i class="material-icons left">error</i>Error al crear backup',
            classes: 'red'
          });
        });
    },
    getSystemInfo() {
      fetch(BASEURL + "api/v1/config/system_info")
        .then((e) => e.json())
        .then((e) => {
          if (e.code == 200) {
            this.systemInfo = e.data;
          }
        });
    },
    initCharts() {
      if (!this.configurations.length) return;

      const ctx = document.getElementById('configDistributionChart');
      if (!ctx) return;

      // Group configuration by type
      const distribution = {};
      this.configurations.forEach(config => {
        distribution[config.config_type] = (distribution[config.config_type] || 0) + 1;
      });

      const labels = Object.keys(distribution).map(l => l.charAt(0).toUpperCase() + l.slice(1));
      const data = Object.values(distribution);

      if (this.charts.configDistribution) {
        this.charts.configDistribution.destroy();
      }

      this.charts.configDistribution = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: [
              '#2196F3', // blue
              '#E91E63', // pink
              '#009688', // teal
              '#4CAF50', // green
              '#673AB7', // deep-purple
              '#FFC107', // amber
              '#F44336', // red
              '#3F51B5'  // indigo
            ],
            borderWidth: 0
          }]
        },
        options: {
          maintainAspectRatio: false,
          legend: {
            position: 'right',
            labels: {
              boxWidth: 12
            }
          }
        }
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getconfigurations(),
        this.getDatabaseBackups(),
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
