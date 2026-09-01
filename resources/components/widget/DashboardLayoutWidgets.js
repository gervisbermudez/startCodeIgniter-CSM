Vue.component("dashboardKpis", {
  template: "#dashboard-kpis-template",
  props: ["kpis", "analyticsUrl"],
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

Vue.component("dashboardWidgetPreview", {
  template: "#dashboard-widget-preview-template",
  props: ["widgetId"],
});

