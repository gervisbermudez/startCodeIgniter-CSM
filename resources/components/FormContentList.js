var FormContentList = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    contents: [],
    tableView: true,
    loader: true,
    filter: "",
  },
  computed: {
    filterContents: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.contents.filter((value, index) => {
          let result =
            value.form_custom.form_name.toLowerCase().indexOf(filterTerm) !=
              -1 || value.user.username.toLowerCase().indexOf(filterTerm) != -1;
          return result;
        });
      } else {
        return this.contents;
      }
    },
  },
  methods: {
    getcontentText: function (content) {
      let data = content.data;
      let text = Object.values(data).join(" ");
      return text.substring(0, 90) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getContentImagePath(content) {
      if (content.imagen_file) {
        return (
          BASEURL +
          content.imagen_file.file_path.substr(2) +
          content.imagen_file.file_name +
          "." +
          content.imagen_file.file_type
        );
      }
      return "https://materializecss.com/images/sample-1.jpg";
    },
    getContents: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/forms/data",
        data: {},
        dataType: "json",
        success: function (response) {
          self.contents = response.data;
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
    deleteContent: function (content, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/forms/data/" + content.form_content_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.contents.splice(index, 1);
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
      this.getContents();
      this.initPlugins();
    });
  },
});
