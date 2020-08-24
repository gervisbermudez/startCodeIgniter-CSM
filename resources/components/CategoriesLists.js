var CategoriesLists = new Vue({
  el: "#root",
  data: {
    categories: [],
    tableView: true,
    loader: true,
    filter: "",
  },
  computed: {
    filterCategories: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.categories.filter((value, index) => {
          let result =
            value.name.toLowerCase().indexOf(filterTerm) != -1 ||
            value.type.toLowerCase().indexOf(filterTerm) != -1
          return result;
        });
      } else {
        return this.categories;
      }
    },
  },
  methods: {
    getcontentText: function (categorie) {
      return categorie.description.substring(0, 50) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(categorie) {
      if (categorie.imagen_file) {
        return (
          BASEURL +
          categorie.imagen_file.file_path.substr(2) +
          categorie.imagen_file.file_name +
          "." +
          categorie.imagen_file.file_type
        );
      }
      return "https://materializecss.com/images/sample-1.jpg";
    },
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/categorie/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.categories = response.data;
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
    deletePage: function (categorie, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/categorie/" + categorie.categorie_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.categories.splice(index, 1);
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