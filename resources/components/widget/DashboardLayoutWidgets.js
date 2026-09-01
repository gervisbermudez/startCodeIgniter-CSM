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
});

Vue.component("dashboardWidgetPreview", {
  template: "#dashboard-widget-preview-template",
  props: ["widgetId"],
});
