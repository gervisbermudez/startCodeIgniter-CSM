var PagesLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    pages: [],
    tableView: false,
    loader: true,
    filter: "",
  },
  mixins: [mixins],
  computed: {
    filterPages: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.pages.filter((value, index) => {
          let result =
            value.subtitle.toLowerCase().indexOf(filterTerm) != -1 ||
            value.title.toLowerCase().indexOf(filterTerm) != -1 ||
            value.template.toLowerCase().indexOf(filterTerm) != -1 ||
            value.username.toLowerCase().indexOf(filterTerm) != -1 ||
            value.layout.toLowerCase().indexOf(filterTerm) != -1 ||
            value.path.toLowerCase().indexOf(filterTerm) != -1;
          return result;
        });
      } else {
        return this.pages;
      }
    },
  },
  methods: {
    getcontentText: function (page) {
      var span = document.createElement("span");
      span.innerHTML = page.content;
      let text = span.textContent || span.innerText;
      return text.substring(0, 120) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(page) {
      if (page.imagen_file) {
        return (
          BASEURL +
          page.imagen_file.file_path.substr(2) +
          page.imagen_file.file_name +
          "." +
          page.imagen_file.file_type
        );
      }
      return "https://materializecss.com/images/sample-1.jpg";
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
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
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
