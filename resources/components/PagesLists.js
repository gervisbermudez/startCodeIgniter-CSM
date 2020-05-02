var PagesLists = new Vue({
  el: "#root",
  data: {
    pages: [],
    tableView: true,
    loader: true,
  },
  methods: {
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.pages = response.data;
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    deletePage: function (page, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/pages/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.pages.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
      this.initPlugins();
    });
  },
});
