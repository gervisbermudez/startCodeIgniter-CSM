function logsT(key, fallback) {
  if (window.CONFIG_I18N && window.CONFIG_I18N[key]) {
    return window.CONFIG_I18N[key];
  }
  return fallback || key;
}

var LogsData = new Vue({
  el: "#root",
  mixins: [mixins],
  data: {
    activeTab: "system",
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
  methods: {
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
    this.activeTab = this.readTabFromUrl();
    this.$nextTick(function () {
      window.addEventListener("popstate", function () {
        self.activeTab = self.readTabFromUrl();
      });
    });
  },
});
