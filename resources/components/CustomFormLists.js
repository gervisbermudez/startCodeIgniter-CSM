var CustomFormLists = new Vue({
  el: "#root",
  data: {
    forms: [],
    tableView: true,
    loader: true,
    filter: "",
  },
  computed: {
    filterForms: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.forms.filter((value, index) => {
          let result =
            value.form_name.toLowerCase().indexOf(filterTerm) != -1 ||
            value.form_description.toLowerCase().indexOf(filterTerm) != -1 ||
            value.user.username.toLowerCase().indexOf(filterTerm) != -1;
          return result;
        });
      } else {
        return this.forms;
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
      return BASEURL + "public/img/default.jpg";
    },
    getForms: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/forms/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.forms = response.data;
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
    deleteForm: function (form, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/forms/" + form.form_custom_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.forms.splice(index, 1);
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
      this.getForms();
      this.initPlugins();
    });
  },
});
