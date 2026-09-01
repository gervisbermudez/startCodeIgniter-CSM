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


Vue.component("albumesWidget", {
  template: "#albumes-widget-template",
  props: ["albumes", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    getPageImagePath(album, index) {
      let item = album.items[index] ? album.items[index] : { file: {} };
      if (item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "/public/img/default.jpg";
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});

Vue.component("createContents", {
  template: "#create-contents-template",
  props: ["forms_types", "content", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
      i18n: window.COLLECTIONS_I18N || {},
    };
  },
  mixins: [mixins],
  methods: {
    toggleStatus: function (item, e) {
      var prev = item.status;
      var status = e.target.checked ? 1 : 2;
      item.status = status;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/models/data_set_status/" + item.custom_model_content_id,
        data: { status: status },
        dataType: "json",
        error: function () {
          item.status = prev;
          e.target.checked = prev == 1 || prev == "1";
          M.toast({ html: (window.COLLECTIONS_I18N && window.COLLECTIONS_I18N.error) || "" });
        },
      });
    },
    getFormsTypeUrl: function (formObject) {
      return BASEURL + "admin/custommodels/addData/" + formObject.custom_model_id;
    },
    base_url: function (path) {
      return BASEURL + path;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: createContents") : null;
    });
  },
});

Vue.component("fileExplorerCollection", {
  template: "#fileExplorerCollection-template",
  props: ["files", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  computed: {
    shortFiles: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.files.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.files
          .filter((file) => {
            return file.file_type != "folder";
          })
          .slice(0, 25);
      }
    },
  },
  methods: {
    getFiles() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            let data = response.data;
            data = data.map((file) => {
              return new ExplorerFile(file);
            });
            self.files = data;
            self.init();
          }
        },
      });
    },
    getFilterFiles(filter_name, filter_value) {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/filter_files",
        data: {
          filter_name: filter_name,
          filter_value: filter_value,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            let data = response.data.map((file) => {
              return new ExplorerFile(file);
            });
            self.files = data;
          }
        },
      });
    },
    filterFiles(filter) {
      var self = this;
      this.fetchLibraryFiles(
        { type: this.normalizeLibraryType(filter) },
        function (response) {
          if (response.code == 200) {
            var data = (response.data || []).map(function (file) {
              return new ExplorerFile(file);
            });
            self.files = data;
          }
        }
      );
    },
    featuredFileServe(file) {
      var self = this;
      file.featured = file.featured == "1" ? "0" : "1";
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/featured_file",
        data: {
          file: file,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Done!" });
          }
        },
      });
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 2000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.init();
      this.debug ? console.log("mounted: fileExplorerCollection") : null;
    });
  },
});

Vue.component("pageCard", {
  template: "#page-card-template",
  props: ["pages"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },

  methods: {
    contentText: function (page) {
      var span = document.createElement("span");
      span.innerHTML = page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 220) + "...";
    },
    getPageFullPath: function (page) {
      if (page.status == 1) {
        return BASEURL + page.path;
      }
    },
    getPageEditPath: function (page) {
      return BASEURL + "admin/pages/editar/" + page.page_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});

Vue.component("usersCollection", {
  template: "#user-collection-template",
  props: ["users", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  methods: {},
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: usersCollection") : null;
    });
  },
});

