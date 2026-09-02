function configT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

function emptyOverview() {
  return {
    site: {
      title: "",
      description: "",
      theme: "",
      public_url: "",
      version: "",
    },
    health: {
      status: "ok",
      score: 100,
      checks: [],
    },
    content: {
      pages: 0,
      drafts: 0,
      files: 0,
      forms: 0,
      messages: 0,
      users: 0,
      collections: 0,
    },
    activity: {
      visits_7: 0,
      unique_visitors_7: 0,
      cms_7: 0,
      api_7: 0,
      messages_7: 0,
      series: { labels: [], visits: [], cms: [] },
    },
    backups: {
      count: 0,
      last_at: null,
      last_filename: null,
    },
  };
}

var ConfiguracionList = new Vue({
  el: "#root",
  data: function () {
    return {
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
    overview: emptyOverview(),
    overviewLoading: false,
    overviewChart: null,
    };
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
    overviewView: function () {
      var empty = emptyOverview();
      var current = this.overview;
      if (!current || typeof current !== "object") {
        return empty;
      }
      var activity = Object.assign({}, empty.activity, current.activity || {});
      activity.series = Object.assign(
        {},
        empty.activity.series,
        (current.activity && current.activity.series) || {}
      );
      return {
        site: Object.assign({}, empty.site, current.site || {}),
        health: Object.assign({}, empty.health, current.health || {}),
        content: Object.assign({}, empty.content, current.content || {}),
        activity: activity,
        backups: Object.assign({}, empty.backups, current.backups || {}),
      };
    },
    healthIssues: function () {
      var map = {
        disk: ["healthDiskTitle", "healthDiskMsg"],
        backup_none: ["healthBackupNoneTitle", "healthBackupNoneMsg"],
        backup_stale: ["healthBackupStaleTitle", "healthBackupStaleMsg"],
        writable: ["healthWritableTitle", "healthWritableMsg"],
        env: ["healthEnvTitle", "healthEnvMsg"],
        analytics: ["healthAnalyticsTitle", "healthAnalyticsMsg"],
        pixel: ["healthPixelTitle", "healthPixelMsg"],
        seo: ["healthSeoTitle", "healthSeoMsg"],
        cleanup: ["healthCleanupTitle", "healthCleanupMsg"],
        logger: ["healthLoggerTitle", "healthLoggerMsg"],
      };
      var issues = [];
      var checks =
        this.overview && this.overview.health && this.overview.health.checks
          ? this.overview.health.checks
          : [];
      checks.forEach(function (check) {
        var keys = map[check.id] || [];
        issues.push({
          id: check.id,
          type: check.type || "info",
          href: check.href || "",
          title: configT(keys[0] || check.id, check.title || check.id),
          message: configT(keys[1] || "", check.message || ""),
        });
      });
      if (this.lastCleanupResult) {
        var totalDeleted =
          (this.lastCleanupResult.system_logs || 0) +
          (this.lastCleanupResult.api_logs || 0) +
          (this.lastCleanupResult.user_tracking || 0);
        if (totalDeleted > 0) {
          issues.push({
            id: "cleanup_done",
            type: "success",
            href: "",
            title: configT("healthCleanupDoneTitle"),
            message: configT("healthCleanupDoneMsg"),
          });
        }
      }
      return issues;
    },
    healthStatus: function () {
      if (this.overview && this.overview.health && this.overview.health.status) {
        return this.overview.health.status;
      }
      return "ok";
    },
    healthStatusLabel: function () {
      if (this.healthStatus === "critical") {
        return configT("healthCritical", "Needs action");
      }
      if (this.healthStatus === "attention") {
        return configT("healthAttention", "Needs attention");
      }
      return configT("healthOk", "Healthy");
    },
    hasOverviewTrend: function () {
      var series = this.overview && this.overview.activity && this.overview.activity.series;
      if (!series) {
        return false;
      }
      var visits = series.visits || [];
      var cms = series.cms || [];
      var hasVisits = visits.some(function (n) {
        return Number(n) > 0;
      });
      var hasCms = cms.some(function (n) {
        return Number(n) > 0;
      });
      return hasVisits || hasCms;
    },
    sitePitch: function () {
      var pages =
        this.overview && this.overview.content ? this.overview.content.pages : 0;
      if (!pages) {
        return configT("valueEmptyPages");
      }
      var visits =
        this.overview && this.overview.activity
          ? this.overview.activity.visits_7
          : 0;
      var cms =
        this.overview && this.overview.activity ? this.overview.activity.cms_7 : 0;
      return configT("valuePitch")
        .replace("%1", this.formatCount(pages))
        .replace("%2", this.formatCount(visits))
        .replace("%3", this.formatCount(cms));
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
  watch: {
    sectionActive: function (section) {
      if (section !== "home") {
        this.destroyOverviewChart();
      }
    },
  },
  methods: {
    formatCount: function (n) {
      var num = Number(n);
      if (isNaN(num)) {
        num = 0;
      }
      try {
        return num.toLocaleString(undefined, {
          maximumFractionDigits: 0,
        });
      } catch (e) {
        return String(num);
      }
    },
    openHealthIssue: function (issue) {
      if (!issue || !issue.href) {
        return;
      }
      if (issue.href.indexOf("admin/configuration?section=") === 0) {
        var section = issue.href.split("section=")[1];
        this.changeSectionActive(section || "home");
        return;
      }
      window.location.href = this.base_url(issue.href);
    },
    getOverview: function () {
      var self = this;
      this.overviewLoading = true;
      fetch(BASEURL + "api/v1/config/overview")
        .then(function (res) {
          return res.json();
        })
        .then(function (response) {
          self.overviewLoading = false;
          if (response && response.code == 200 && response.data) {
            var base = emptyOverview();
            var payload = response.data;
            var activity = Object.assign({}, base.activity, payload.activity || {});
            activity.series = Object.assign(
              {},
              base.activity.series,
              (payload.activity && payload.activity.series) || {}
            );
            self.overview = {
              site: Object.assign({}, base.site, payload.site || {}),
              health: Object.assign({}, base.health, payload.health || {}),
              content: Object.assign({}, base.content, payload.content || {}),
              activity: activity,
              backups: Object.assign({}, base.backups, payload.backups || {}),
            };
            self.$nextTick(function () {
              self.renderOverviewChart();
            });
          }
        })
        .catch(function () {
          self.overviewLoading = false;
        });
    },
    cssVar: function (name, fallback) {
      var value = getComputedStyle(document.documentElement).getPropertyValue(name);
      return (value && value.trim()) || fallback;
    },
    hexToRgba: function (color, alpha) {
      var c = (color || "").trim();
      if (c.indexOf("rgb(") === 0) {
        return c.replace("rgb(", "rgba(").replace(")", ", " + alpha + ")");
      }
      if (c.indexOf("rgba(") === 0) {
        return c;
      }
      var hex = c.replace("#", "");
      if (hex.length === 3) {
        hex =
          hex.charAt(0) +
          hex.charAt(0) +
          hex.charAt(1) +
          hex.charAt(1) +
          hex.charAt(2) +
          hex.charAt(2);
      }
      var n = parseInt(hex, 16);
      if (isNaN(n)) {
        return "rgba(38, 166, 154, " + alpha + ")";
      }
      return (
        "rgba(" +
        ((n >> 16) & 255) +
        ", " +
        ((n >> 8) & 255) +
        ", " +
        (n & 255) +
        ", " +
        alpha +
        ")"
      );
    },
    formatChartDay: function (iso) {
      var parts = String(iso).split("-");
      if (parts.length < 3) {
        return iso;
      }
      var months = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ];
      var month = months[parseInt(parts[1], 10) - 1] || parts[1];
      return parseInt(parts[2], 10) + " " + month;
    },
    destroyOverviewChart: function () {
      if (typeof Chart !== "undefined" && Chart.getChart) {
        var existing = Chart.getChart("overviewTrendChart");
        if (existing) {
          existing.destroy();
        }
      }
      this.overviewChart = null;
    },
    renderOverviewChart: function () {
      var self = this;
      this.destroyOverviewChart();
      if (this.sectionActive !== "home" || !this.hasOverviewTrend || typeof Chart === "undefined") {
        return;
      }
      var canvas = document.getElementById("overviewTrendChart");
      if (!canvas) {
        return;
      }
      var series = this.overview.activity.series || {};
      var labels = (series.labels || []).map(function (d) {
        return self.formatChartDay(d);
      });
      var line = this.cssVar("--st-interactive", "#26A69A");
      var accent = this.cssVar("--st-accent", "#fb9678");
      var muted = this.cssVar("--st-text-secondary", "#5D5D5D");
      var grid = this.cssVar("--st-border", "#E5E5E5");
      var tooltip = this.cssVar("--st-chrome", "#646b7f");
      var surface = this.cssVar("--st-surface", "#fff");
      var ctx = canvas.getContext("2d");
      this.overviewChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: configT("activityVisits", "Visits"),
              data: series.visits || [],
              borderColor: line,
              backgroundColor: self.hexToRgba(line, 0.12),
              tension: 0.4,
              fill: true,
              pointRadius: 0,
              pointHoverRadius: 5,
              pointHoverBackgroundColor: line,
              pointHoverBorderColor: surface,
              pointHoverBorderWidth: 2,
              borderWidth: 2.25,
            },
            {
              label: configT("activityCms", "CMS activity"),
              data: series.cms || [],
              borderColor: accent,
              backgroundColor: "transparent",
              tension: 0.4,
              fill: false,
              pointRadius: 0,
              pointHoverRadius: 5,
              pointHoverBackgroundColor: accent,
              pointHoverBorderColor: surface,
              pointHoverBorderWidth: 2,
              borderWidth: 2,
              borderDash: [4, 3],
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: tooltip,
              titleColor: "#fff",
              bodyColor: "#fff",
              titleFont: { size: 12, weight: "500", family: "Roboto, sans-serif" },
              bodyFont: { size: 12, family: "Roboto, sans-serif" },
              padding: { top: 8, right: 12, bottom: 8, left: 12 },
              cornerRadius: 8,
              caretSize: 5,
            },
          },
          layout: { padding: { top: 8, right: 8, left: 4, bottom: 0 } },
          scales: {
            x: {
              ticks: {
                color: muted,
                maxRotation: 0,
                autoSkip: true,
                maxTicksLimit: 6,
                font: { size: 11, family: "Roboto, sans-serif" },
              },
              grid: { display: false, drawBorder: false },
              border: { display: false },
            },
            y: {
              beginAtZero: true,
              ticks: {
                color: muted,
                precision: 0,
                maxTicksLimit: 5,
                font: { size: 11, family: "Roboto, sans-serif" },
                padding: 8,
              },
              grid: {
                color: self.hexToRgba(grid, 0.9),
                drawTicks: false,
                drawBorder: false,
              },
              border: { display: false },
            },
          },
        },
      });
    },
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
        this.getOverview();
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
