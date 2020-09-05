var ConfiguracionList = new Vue({
  el: "#root",
  data: {
    configurations: [],
    tableView: true,
    loader: true,
    filter: "",
  },
  computed: {
    filterConfigurations: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.configurations.filter((value, index) => {
          let result =
            value.config_name.toLowerCase().indexOf(filterTerm) != -1 ||
            /*  value.config_name.user.username.toLowerCase().indexOf(filterTerm) !=
              -1 ||
            value.config_name.user.user_data.nombre
              .toLowerCase()
              .indexOf(filterTerm) != -1 ||
            value.config_name.user.user_data.apellido
              .toLowerCase()
              .indexOf(filterTerm) != -1 || */
            value.config_value.toLowerCase().indexOf(filterTerm) != -1 ||
            value.config_value.toLowerCase().indexOf(filterTerm) != -1 ||
            value.config_value.toLowerCase().indexOf(filterTerm) != -1 ||
            value.config_type.toLowerCase().indexOf(filterTerm) != -1;
          return result;
        });
      } else {
        return this.configurations;
      }
    },
  },
  methods: {
    toggleEddit(index) {
      this.configurations[index].editable = !this.configurations[index]
        .editable;
      this.$forceUpdate();
    },
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
    saveConfig(index, callBack) {
      this.toggleEddit(index);
      var self = this;
      var url = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: url,
        data: self.configurations[index],
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          setTimeout(() => {
            self.loader = false;
          }, 1500);
          if (response.code == 200) {
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: "Config Saved!" });
            self.loader = false;
          }
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
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
    getconfigurations: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/",
        data: {},
        dataType: "json",
        success: function (response) {
          let configurations = response.data;
          for (const key in configurations) {
            if (configurations.hasOwnProperty(key)) {
              configurations[key].user = new User(configurations[key].user);
            }
          }
          self.configurations = configurations;
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
            self.configurations.splice(index, 1);
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
      this.getconfigurations();
      this.initPlugins();
    });
  },
});
