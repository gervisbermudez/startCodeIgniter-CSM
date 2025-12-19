var CategoriesLists = new Vue({
  el: "#root",
  data: {
    categories: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
  },
  mixins: [mixins],
  computed: {
    filterCategories: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.categories.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
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
      return BASEURL + "public/img/default.jpg";
    },
    getCategories: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/categories/",
        data: {},
        dataType: "json",
        success: function (response) {
          let categories = response.data;
          for (const key in categories) {
            if (categories.hasOwnProperty(key)) {
              categories[key].user = new User(categories[key].user);
            }
          }
          self.categories = categories;
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    delete: function (categorie, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/categories/" + categorie.categorie_id,
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
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.delete(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        if (elems.length > 0) {
          var instances = M.Tooltip.init(elems, {});
        }
        var elems = document.querySelectorAll(".dropdown-trigger");
        if (elems.length > 0) {
          var instances = M.Dropdown.init(elems, {});
        }
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getCategories();
    });
  },
});
