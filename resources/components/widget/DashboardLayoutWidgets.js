Vue.component("dashboardKpis", {
  template: "#dashboard-kpis-template",
  props: ["kpis", "analyticsUrl"],
});

Vue.component("dashboardKpiCard", {
  template: "#dashboard-kpi-card-template",
  props: ["kpis", "analyticsUrl", "widgetId"],
});

Vue.component("dashboardCharts", {
  template: "#dashboard-charts-template",
  props: [
    "canViewAnalytics",
    "hasAnalyticsData",
    "hasReferrers",
    "stats",
    "graphs",
    "topPages",
    "analyticsUrl",
    "loader",
  ],
});

Vue.component("dashboardChartPanel", {
  template: "#dashboard-chart-panel-template",
  props: [
    "widgetId",
    "canViewAnalytics",
    "hasAnalyticsData",
    "hasReferrers",
    "stats",
    "graphs",
    "topPages",
    "analyticsUrl",
    "loader",
  ],
});

Vue.component("dashboardWelcome", {
  template: "#dashboard-welcome-template",
  props: ["counts"],
});

Vue.component("dashboardCreator", {
  template: "#dashboard-creator-template",
  props: ["creator", "creatorModes"],
});

Vue.component("dashboardDrafts", {
  template: "#dashboard-drafts-template",
  props: ["drafts"],
});

Vue.component("dashboardTimeline", {
  template: "#dashboard-timeline-template",
  props: ["timeline", "defaultAvatar"],
});

Vue.component("dashboardEvents", {
  template: "#dashboard-events-template",
  props: ["events"],
});

Vue.component("dashboardSiteStatus", {
  template: "#dashboard-site-status-template",
  props: ["site"],
});

Vue.component("dashboardQuickSettings", {
  template: "#dashboard-quick-settings-template",
  props: ["site"],
});

Vue.component("dashboardFragments", {
  template: "#dashboard-fragments-template",
  props: ["fragments"],
});

Vue.component("dashboardInbox", {
  template: "#dashboard-inbox-template",
  props: ["inbox"],
});

Vue.component("dashboardCalendar", {
  template: "#dashboard-calendar-template",
  props: ["events", "calendarEvents"],
  data: function () {
    var now = new Date();
    var month = now.getMonth() + 1;
    var day = now.getDate();
    var key =
      now.getFullYear() +
      "-" +
      (month < 10 ? "0" : "") +
      month +
      "-" +
      (day < 10 ? "0" : "") +
      day;
    return {
      viewYear: now.getFullYear(),
      viewMonth: now.getMonth(),
      selectedKey: key,
    };
  },
  computed: {
    weekdays: function () {
      var raw =
        typeof lang === "function" ? lang("dashboard_cal_dow") : "Sun Mon Tue Wed Thu Fri Sat";
      return String(raw || "")
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 7);
    },
    eventList: function () {
      if (this.calendarEvents && this.calendarEvents.length) {
        return this.calendarEvents;
      }
      return this.events || [];
    },
    monthLabel: function () {
      var d = new Date(this.viewYear, this.viewMonth, 1);
      try {
        return d.toLocaleDateString(undefined, { month: "long", year: "numeric" });
      } catch (e) {
        return this.viewMonth + 1 + "/" + this.viewYear;
      }
    },
    cells: function () {
      var y = this.viewYear;
      var m = this.viewMonth;
      var first = new Date(y, m, 1);
      var start = first.getDay();
      var days = new Date(y, m + 1, 0).getDate();
      var today = new Date();
      var todayKey = this.dateKey(today.getFullYear(), today.getMonth() + 1, today.getDate());
      var counts = this.eventCounts;
      var out = [];
      var i;
      for (i = 0; i < start; i++) {
        out.push({
          key: "pad-" + i,
          pad: true,
          inMonth: false,
          day: "",
          isToday: false,
          hasEvents: false,
        });
      }
      for (i = 1; i <= days; i++) {
        var key = this.dateKey(y, m + 1, i);
        out.push({
          key: key,
          pad: false,
          inMonth: true,
          day: i,
          isToday: key === todayKey,
          hasEvents: !!counts[key],
        });
      }
      return out;
    },
    eventCounts: function () {
      var map = {};
      (this.eventList || []).forEach(function (ev) {
        var key = String(ev.date_start || "").slice(0, 10);
        if (!key) {
          return;
        }
        map[key] = (map[key] || 0) + 1;
      });
      return map;
    },
    selectedEvents: function () {
      var key = this.selectedKey;
      return (this.eventList || []).filter(function (ev) {
        return String(ev.date_start || "").slice(0, 10) === key;
      });
    },
  },
  methods: {
    dateKey: function (y, m, d) {
      return (
        y +
        "-" +
        (m < 10 ? "0" : "") +
        m +
        "-" +
        (d < 10 ? "0" : "") +
        d
      );
    },
    shiftMonth: function (dir) {
      var m = this.viewMonth + dir;
      var y = this.viewYear;
      if (m < 0) {
        m = 11;
        y -= 1;
      }
      if (m > 11) {
        m = 0;
        y += 1;
      }
      this.viewMonth = m;
      this.viewYear = y;
    },
    selectDay: function (cell) {
      if (!cell || !cell.inMonth) {
        return;
      }
      this.selectedKey = cell.key;
    },
  },
});

Vue.component("dashboardPagePulse", {
  template: "#dashboard-page-pulse-template",
  props: ["counts"],
  computed: {
    pulseRows: function () {
      var c = this.counts || {};
      var pub = parseInt(c.pages_published, 10) || 0;
      var draft = parseInt(c.pages_draft, 10) || 0;
      var arch = parseInt(c.pages_archived, 10) || 0;
      var total = pub + draft + arch;
      if (total < 1) {
        total = 1;
      }
      return [
        { key: "published", value: pub, pct: Math.round((pub / total) * 100) },
        { key: "draft", value: draft, pct: Math.round((draft / total) * 100) },
        { key: "archived", value: arch, pct: Math.round((arch / total) * 100) },
      ];
    },
  },
});

Vue.component("dashboardPublished", {
  template: "#dashboard-published-template",
  props: ["published", "total"],
});

Vue.component("dashboardMenus", {
  template: "#dashboard-menus-template",
  props: ["menus", "total"],
});

Vue.component("dashboardCategories", {
  template: "#dashboard-categories-template",
  props: ["categories", "total"],
});

Vue.component("dashboardVideos", {
  template: "#dashboard-videos-template",
  props: ["videos", "total"],
});

Vue.component("dashboardWidgetPreview", {
  template: "#dashboard-widget-preview-template",
  props: ["widgetId"],
});
