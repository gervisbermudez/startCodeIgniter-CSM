function logsT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

function emptyLogsSummary() {
  return {
    total: 0,
    last_7: 0,
    today: 0,
    last_at: null,
    peak: { label: null, total: 0 },
    avg_day: 0,
    series: { labels: [], values: [] },
    breakdown: [],
    top_pages: [],
    top_actions: [],
    top_people: [],
    top_uris: [],
    rejected: 0,
    unique_visitors: 0,
  };
}

var LogsData = new Vue({
  el: "#root",
  mixins: [mixins],
  data: {
    activeTab: (function () {
      try {
        var tab = new URLSearchParams(window.location.search).get("tab") || "system";
        if (tab !== "api" && tab !== "tracking") {
          return "system";
        }
        return tab;
      } catch (e) {
        return "system";
      }
    })(),
    summary: emptyLogsSummary(),
    summaryLoading: false,
    chartPanel: "activity",
    insightsOpen: (function () {
      try {
        return window.matchMedia("(min-width: 993px)").matches;
      } catch (e) {
        return true;
      }
    })(),
    charts: {
      trend: null,
      breakdown: null,
    },
    selectedEntry: null,
    listType: "",
    listToken: "",
    listMethod: "",
    listAuthorized: "",
    listDevice: "",
  },
  computed: {
    endpoint: function () {
      if (this.activeTab === "api") {
        return "api/v1/config/apilogger/";
      }
      if (this.activeTab === "tracking") {
        return "api/v1/config/usertrackinglogger/";
      }
      return "api/v1/config/systemlogger/";
    },
    index_data: function () {
      if (this.activeTab === "api") {
        return "api_log_id";
      }
      if (this.activeTab === "tracking") {
        return "user_tracking_id";
      }
      return "logger_id";
    },
    emptyTitle: function () {
      return logsT("logsEmpty", "No log entries");
    },
    hasTrend: function () {
      var values = this.summary.series && this.summary.series.values;
      if (!values || !values.length) {
        return false;
      }
      return values.some(function (n) {
        return Number(n) > 0;
      });
    },
    hasBreakdown: function () {
      return this.summary.breakdown && this.summary.breakdown.length > 0;
    },
    sourceLede: function () {
      if (this.activeTab === "api") {
        return logsT("logsSourceApi", "");
      }
      if (this.activeTab === "tracking") {
        return logsT("logsSourceTracking", "");
      }
      return logsT("logsSourceSystem", "");
    },
    chartHelp: function () {
      var mix = this.chartPanel === "mix";
      if (this.activeTab === "api") {
        return mix ? logsT("logsHelpMixApi", "") : logsT("logsHelpActivityApi", "");
      }
      if (this.activeTab === "tracking") {
        return mix ? logsT("logsHelpMixTracking", "") : logsT("logsHelpActivityTracking", "");
      }
      return mix ? logsT("logsHelpMixSystem", "") : logsT("logsHelpActivitySystem", "");
    },
    breakdownLegend: function () {
      var slices = this.chartPalette().slices;
      var rows = this.summary.breakdown || [];
      return rows.map(function (row, index) {
        return {
          label: row.label,
          total: row.total,
          color: slices[index % slices.length],
        };
      });
    },
    detailsPrimary: function () {
      if (this.activeTab === "api") {
        return this.summary.top_uris || [];
      }
      if (this.activeTab === "tracking") {
        return this.summary.top_pages || [];
      }
      return this.summary.top_actions || [];
    },
    detailsPrimaryTitle: function () {
      if (this.activeTab === "api") {
        return logsT("logsTopUris", "URLs called most often");
      }
      if (this.activeTab === "tracking") {
        return logsT("logsTopPages", "Pages opened most often");
      }
      return logsT("logsTopActions", "Most common actions");
    },
    detailsSecondary: function () {
      if (this.activeTab === "system") {
        return this.summary.top_people || [];
      }
      return [];
    },
    detailsSecondaryTitle: function () {
      return logsT("logsTopPeople", "Who did the most");
    },
    tableKey: function () {
      return [
        this.activeTab,
        this.listType,
        this.listToken,
        this.listMethod,
        this.listAuthorized,
        this.listDevice,
      ].join("|");
    },
    listQueryParams: function () {
      var q = {};
      if (this.activeTab === "system") {
        if (this.listType) {
          q.type = this.listType;
        }
        if (this.listToken) {
          q.token = this.listToken;
        }
      }
      if (this.activeTab === "api") {
        if (this.listMethod) {
          q.method = this.listMethod;
        }
        if (this.listAuthorized !== "") {
          q.authorized = this.listAuthorized;
        }
      }
      if (this.activeTab === "tracking" && this.listDevice) {
        q.device_type = this.listDevice;
      }
      return q;
    },
    selectedId: function () {
      if (!this.selectedEntry) {
        return null;
      }
      var key = this.index_data;
      return this.selectedEntry[key] || null;
    },
    primaryFilterChips: function () {
      var rows = this.summary.breakdown || [];
      return rows.map(function (row) {
        return { value: String(row.label || ""), label: String(row.label || "") };
      }).filter(function (chip) {
        return chip.value && chip.value !== "—";
      });
    },
    primaryFilterValue: function () {
      if (this.activeTab === "api") {
        return this.listMethod;
      }
      if (this.activeTab === "tracking") {
        return this.listDevice;
      }
      return this.listType;
    },
    secondaryFilterChips: function () {
      if (this.activeTab === "system") {
        return (this.summary.top_actions || []).map(function (row) {
          return { value: String(row.label || ""), label: String(row.label || "") };
        });
      }
      if (this.activeTab === "api") {
        return [
          { value: "1", label: logsT("logsAuthorizedYes", "Allowed") },
          { value: "0", label: logsT("logsAuthorizedNo", "Rejected") },
        ];
      }
      return [];
    },
    secondaryFilterValue: function () {
      if (this.activeTab === "system") {
        return this.listToken;
      }
      if (this.activeTab === "api") {
        return this.listAuthorized;
      }
      return "";
    },
    inspectorFields: function () {
      var item = this.selectedEntry;
      if (!item) {
        return [];
      }
      if (this.activeTab === "api") {
        return this.apiInspectorFields(item);
      }
      if (this.activeTab === "tracking") {
        return this.trackingInspectorFields(item);
      }
      return this.systemInspectorFields(item);
    },
    colums: function () {
      if (this.activeTab === "api") {
        return [
          { colum: "uri", label: logsT("colUri", "URI") },
          { colum: "method", label: logsT("colMethod", "Method") },
          { colum: "ip_address", label: logsT("colIp", "IP") },
          { colum: "date_create", label: logsT("colCreated", "Date") },
          { colum: "authorized", label: logsT("colAuthorized", "Authorized") },
          { colum: "status", label: logsT("colStatus", "Status"), handler: "publish" },
        ];
      }
      if (this.activeTab === "tracking") {
        return [
          { colum: "requested_url", label: logsT("colUri", "URI") },
          { colum: "referer_page", label: logsT("colReferer", "Referer") },
          { colum: "page_name", label: logsT("colPage", "Page") },
          { colum: "client_ip", label: logsT("colIp", "IP") },
          { colum: "query_string", label: logsT("colQuery", "Query") },
          { colum: "date_create", label: logsT("colCreated", "Date") },
          { colum: "status", label: logsT("colStatus", "Status"), handler: "publish" },
        ];
      }
      return [
        { colum: "user", label: logsT("colAuthor", "Author") },
        { colum: "comment", label: logsT("colComment", "Comment") },
        { colum: "type", label: logsT("colType", "Type") },
        { colum: "token", label: logsT("colToken", "Token") },
        { colum: "type_description", label: logsT("colDescription", "Description") },
        {
          colum: "type_link",
          label: logsT("colView", "View"),
          format: function (item) {
            if (!item.type_link) {
              return "";
            }
            return '<a href="' + item.type_link + '">' + logsT("colView", "View") + "</a>";
          },
        },
        { colum: "date_create", label: logsT("colCreated", "Created") },
        { colum: "status", label: logsT("colStatus", "Status"), handler: "publish" },
      ];
    },
  },
  watch: {
    activeTab: function () {
      this.chartPanel = "activity";
      this.resetListFilters();
      this.closeInspector();
      this.loadSummary();
    },
    chartPanel: function () {
      if (this.insightsOpen) {
        this.renderCharts();
      }
    },
    insightsOpen: function (open) {
      if (open) {
        this.renderCharts();
      } else {
        this.destroyCharts();
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
          maximumFractionDigits: 1,
        });
      } catch (e) {
        return String(num);
      }
    },
    parseLogDate: function (value) {
      if (!value) {
        return null;
      }
      var raw = String(value).trim();
      var iso = raw.indexOf("T") === -1 ? raw.replace(" ", "T") : raw;
      var d = new Date(iso);
      if (isNaN(d.getTime())) {
        d = new Date(raw);
      }
      return isNaN(d.getTime()) ? null : d;
    },
    formatWhen: function (value) {
      var d = this.parseLogDate(value);
      if (!d) {
        return value || "—";
      }
      try {
        return d.toLocaleString(undefined, {
          day: "numeric",
          month: "short",
          hour: "2-digit",
          minute: "2-digit",
        });
      } catch (e) {
        return String(value);
      }
    },
    formatDay: function (value) {
      if (!value) {
        return "—";
      }
      var d = this.parseLogDate(String(value).length === 10 ? value + "T00:00:00" : value);
      if (!d) {
        return String(value);
      }
      try {
        return d.toLocaleDateString(undefined, {
          day: "numeric",
          month: "short",
        });
      } catch (e) {
        return String(value);
      }
    },
    resetListFilters: function () {
      this.listType = "";
      this.listToken = "";
      this.listMethod = "";
      this.listAuthorized = "";
      this.listDevice = "";
    },
    setPrimaryFilter: function (value) {
      var next = this.primaryFilterValue === value ? "" : value;
      if (this.activeTab === "api") {
        this.listMethod = next;
      } else if (this.activeTab === "tracking") {
        this.listDevice = next;
      } else {
        this.listType = next;
      }
      this.closeInspector();
    },
    setSecondaryFilter: function (value) {
      var next = this.secondaryFilterValue === value ? "" : value;
      if (this.activeTab === "api") {
        this.listAuthorized = next;
      } else {
        this.listToken = next;
      }
      this.closeInspector();
    },
    openInspector: function (item) {
      this.selectedEntry = item || null;
    },
    closeInspector: function () {
      this.selectedEntry = null;
    },
    fieldRow: function (key, label, value, kind) {
      if (value === null || value === undefined || value === "") {
        return null;
      }
      return {
        key: key,
        label: label,
        value: value,
        kind: kind || "text",
      };
    },
    displayUser: function (item) {
      if (!item || !item.user) {
        return "";
      }
      var user = item.user;
      if (typeof user.get_fullname === "function") {
        return user.get_fullname();
      }
      if (user.user_data && (user.user_data.nombre || user.user_data.apellido)) {
        return [user.user_data.nombre, user.user_data.apellido].filter(Boolean).join(" ");
      }
      return user.username || "";
    },
    authorizedLabel: function (value) {
      var raw = String(value);
      if (raw === "1" || raw === "true") {
        return logsT("logsAuthorizedYes", "Allowed");
      }
      return logsT("logsAuthorizedNo", "Rejected");
    },
    systemInspectorFields: function (item) {
      var rows = [
        this.fieldRow("author", logsT("colAuthor", "Author"), this.displayUser(item)),
        this.fieldRow("comment", logsT("colComment", "Comment"), item.comment),
        this.fieldRow("type", logsT("colType", "Type"), item.type),
        this.fieldRow("token", logsT("logsAction", "Action"), item.token),
        this.fieldRow("type_description", logsT("colDescription", "Description"), item.type_description),
        this.fieldRow("date_create", logsT("colCreated", "Created"), this.formatWhen(item.date_create)),
      ];
      if (item.type_link) {
        rows.push({
          key: "type_link",
          label: logsT("colView", "View"),
          value: item.type_link,
          kind: "link",
        });
      }
      return rows.filter(Boolean);
    },
    apiInspectorFields: function (item) {
      var rows = [
        this.fieldRow("method", logsT("colMethod", "Method"), item.method),
        this.fieldRow("uri", logsT("colUri", "URI"), item.uri),
        this.fieldRow("ip_address", logsT("colIp", "IP"), item.ip_address),
        this.fieldRow("authorized", logsT("colAuthorized", "Authorized"), this.authorizedLabel(item.authorized)),
        this.fieldRow("response_code", logsT("colStatus", "Status"), item.response_code),
        this.fieldRow("date_create", logsT("colCreated", "Date"), this.formatWhen(item.date_create)),
      ];
      var payload = this.formatPayload(item.params);
      rows.push({
        key: "params",
        label: logsT("logsPayload", "Request payload"),
        value: payload || logsT("logsEmptyPayload", "No payload was stored."),
        kind: "payload",
      });
      return rows.filter(Boolean);
    },
    trackingInspectorFields: function (item) {
      return [
        this.fieldRow("page_name", logsT("colPage", "Page"), item.page_name),
        this.fieldRow("requested_url", logsT("colUri", "URI"), item.requested_url),
        this.fieldRow("referer_page", logsT("colReferer", "Referer"), item.referer_page),
        this.fieldRow("query_string", logsT("colQuery", "Query"), item.query_string),
        this.fieldRow("client_ip", logsT("colIp", "IP"), item.client_ip),
        this.fieldRow("device_type", logsT("logsDevice", "Device"), item.device_type),
        this.fieldRow("browser", logsT("logsBrowser", "Browser"), item.browser),
        this.fieldRow("platform", logsT("logsPlatform", "Platform"), item.platform),
        this.fieldRow("user_agent", logsT("logsUserAgent", "Browser details"), item.user_agent),
        this.fieldRow("date_create", logsT("colCreated", "Date"), this.formatWhen(item.date_create)),
      ].filter(Boolean);
    },
    formatPayload: function (raw) {
      if (raw === null || raw === undefined || raw === "") {
        return "";
      }
      var parsed = raw;
      if (typeof raw === "string") {
        try {
          parsed = JSON.parse(raw);
        } catch (e) {
          return this.truncateText(raw, 8000);
        }
      }
      parsed = this.redactPayload(parsed);
      try {
        return this.truncateText(JSON.stringify(parsed, null, 2), 8000);
      } catch (e2) {
        return this.truncateText(String(raw), 8000);
      }
    },
    redactPayload: function (value) {
      var self = this;
      var secret = /password|passwd|secret|token|authorization|api[_-]?key/i;
      if (Array.isArray(value)) {
        return value.map(function (item) {
          return self.redactPayload(item);
        });
      }
      if (value && typeof value === "object") {
        var out = {};
        Object.keys(value).forEach(function (key) {
          out[key] = secret.test(key) ? logsT("logsRedacted", "Hidden") : self.redactPayload(value[key]);
        });
        return out;
      }
      return value;
    },
    truncateText: function (text, max) {
      var str = String(text);
      if (str.length <= max) {
        return str;
      }
      return str.slice(0, max) + "…";
    },
    toggleInsights: function () {
      this.insightsOpen = !this.insightsOpen;
    },
    setChartPanel: function (panel) {
      this.chartPanel = panel === "mix" ? "mix" : "activity";
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
        hex = hex.charAt(0) + hex.charAt(0) + hex.charAt(1) + hex.charAt(1) + hex.charAt(2) + hex.charAt(2);
      }
      var n = parseInt(hex, 16);
      if (isNaN(n)) {
        return "rgba(38, 166, 154, " + alpha + ")";
      }
      return "rgba(" + ((n >> 16) & 255) + ", " + ((n >> 8) & 255) + ", " + (n & 255) + ", " + alpha + ")";
    },
    chartPalette: function () {
      return {
        text: this.cssVar("--st-text", "#444"),
        muted: this.cssVar("--st-text-secondary", "#5D5D5D"),
        grid: this.cssVar("--st-border", "#E5E5E5"),
        line: this.cssVar("--st-interactive", "#26A69A"),
        surface: this.cssVar("--st-surface", "#fff"),
        tooltip: this.cssVar("--st-chrome", "#646b7f"),
        slices: [
          this.cssVar("--st-interactive", "#26A69A"),
          this.cssVar("--st-accent", "#fb9678"),
          this.cssVar("--st-chrome", "#646b7f"),
          this.cssVar("--st-warning", "#ff9800"),
          this.cssVar("--st-success", "#4CAF50"),
          this.cssVar("--st-neutral", "#757575"),
          this.cssVar("--st-danger", "#F44336"),
          this.cssVar("--st-trash", "#424242"),
        ],
      };
    },
    formatChartDay: function (iso) {
      var parts = String(iso).split("-");
      if (parts.length < 3) {
        return iso;
      }
      var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      var month = months[parseInt(parts[1], 10) - 1] || parts[1];
      return parseInt(parts[2], 10) + " " + month;
    },
    chartTooltip: function (palette) {
      return {
        backgroundColor: palette.tooltip,
        titleColor: "#fff",
        bodyColor: "#fff",
        titleFont: { size: 12, weight: "500", family: "Roboto, sans-serif" },
        bodyFont: { size: 12, family: "Roboto, sans-serif" },
        padding: { top: 8, right: 12, bottom: 8, left: 12 },
        cornerRadius: 8,
        displayColors: false,
        caretSize: 5,
      };
    },
    destroyChart: function (canvasId, key) {
      if (typeof Chart !== "undefined" && Chart.getChart) {
        var existing = Chart.getChart(canvasId);
        if (existing) {
          existing.destroy();
        }
      }
      if (this.charts[key]) {
        this.charts[key] = null;
      }
    },
    destroyCharts: function () {
      this.destroyChart("logsTrendChart", "trend");
      this.destroyChart("logsBreakdownChart", "breakdown");
    },
    renderCharts: function () {
      var self = this;
      this.destroyCharts();
      if (!this.insightsOpen) {
        return;
      }
      this.$nextTick(function () {
        if (self.chartPanel === "mix") {
          self.renderBreakdownChart();
        } else {
          self.renderTrendChart();
        }
      });
    },
    renderTrendChart: function () {
      var canvas = document.getElementById("logsTrendChart");
      this.destroyChart("logsTrendChart", "trend");
      if (!canvas || !this.hasTrend || typeof Chart === "undefined") {
        return;
      }
      var palette = this.chartPalette();
      var ctx = canvas.getContext("2d");
      var height = canvas.parentNode ? canvas.parentNode.clientHeight : 180;
      var gradient = ctx.createLinearGradient(0, 0, 0, height);
      gradient.addColorStop(0, this.hexToRgba(palette.line, 0.28));
      gradient.addColorStop(0.85, this.hexToRgba(palette.line, 0.03));
      gradient.addColorStop(1, this.hexToRgba(palette.line, 0));
      var self = this;
      var labels = (this.summary.series.labels || []).map(function (d) {
        return self.formatChartDay(d);
      });
      this.charts.trend = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: logsT("logsCount", "Events"),
              data: this.summary.series.values,
              borderColor: palette.line,
              backgroundColor: gradient,
              tension: 0.4,
              fill: true,
              pointRadius: 0,
              pointHoverRadius: 5,
              pointHoverBackgroundColor: palette.line,
              pointHoverBorderColor: palette.surface,
              pointHoverBorderWidth: 2,
              borderWidth: 2.25,
              borderCapStyle: "round",
              borderJoinStyle: "round",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: { mode: "index", intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: this.chartTooltip(palette),
          },
          layout: { padding: { top: 8, right: 8, left: 4, bottom: 0 } },
          scales: {
            x: {
              ticks: {
                color: palette.muted,
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
                color: palette.muted,
                precision: 0,
                maxTicksLimit: 5,
                font: { size: 11, family: "Roboto, sans-serif" },
                padding: 8,
              },
              grid: {
                color: this.hexToRgba(palette.grid, 0.9),
                drawTicks: false,
                drawBorder: false,
              },
              border: { display: false },
            },
          },
        },
      });
    },
    renderBreakdownChart: function () {
      var canvas = document.getElementById("logsBreakdownChart");
      this.destroyChart("logsBreakdownChart", "breakdown");
      if (!canvas || !this.hasBreakdown || typeof Chart === "undefined") {
        return;
      }
      var palette = this.chartPalette();
      var rows = this.summary.breakdown;
      var tooltip = this.chartTooltip(palette);
      this.charts.breakdown = new Chart(canvas.getContext("2d"), {
        type: "doughnut",
        data: {
          labels: rows.map(function (row) {
            return row.label;
          }),
          datasets: [
            {
              data: rows.map(function (row) {
                return row.total;
              }),
              backgroundColor: palette.slices,
              borderColor: palette.surface,
              borderWidth: 3,
              spacing: 2,
              hoverOffset: 4,
              borderRadius: 3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "72%",
          plugins: {
            legend: { display: false },
            tooltip: tooltip,
          },
        },
      });
    },
    loadSummary: function () {
      var self = this;
      this.summaryLoading = true;
      this.destroyCharts();
      fetch(BASEURL + "api/v1/config/logs_summary?source=" + encodeURIComponent(this.activeTab), {
        credentials: "same-origin",
        headers: { Accept: "application/json" },
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (payload) {
          if (payload && (payload.code == 200 || payload.code === "200") && payload.data) {
            self.summary = Object.assign(emptyLogsSummary(), payload.data);
          } else {
            self.summary = emptyLogsSummary();
          }
        })
        .catch(function () {
          self.summary = emptyLogsSummary();
        })
        .then(function () {
          self.summaryLoading = false;
          self.renderCharts();
        });
    },
    readTabFromUrl: function () {
      var tab = "system";
      try {
        var params = new URLSearchParams(window.location.search);
        tab = params.get("tab") || "system";
      } catch (e) {
        tab = "system";
      }
      if (tab !== "api" && tab !== "tracking") {
        return "system";
      }
      return tab;
    },
    changeTab: function (tab) {
      if (tab !== "api" && tab !== "tracking") {
        tab = "system";
      }
      this.activeTab = tab;
      var url =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?tab=" +
        tab;
      window.history.pushState({ path: url }, "", url);
    },
  },
  mounted: function () {
    var self = this;
    this.loadSummary();
    this.$nextTick(function () {
      if (typeof self.initPlugins === "function") {
        self.initPlugins();
      }
      window.addEventListener("popstate", function () {
        self.activeTab = self.readTabFromUrl();
      });
    });
  },
  beforeDestroy: function () {
    this.destroyCharts();
  },
});
