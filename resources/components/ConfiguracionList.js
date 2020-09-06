var ConfiguracionList = new Vue({
  el: "#root",
  data: {
    configurations: [],
    tableView: false,
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
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    toggleEddit(configuration) {
      configuration.editable = !configuration.editable;
      this.$forceUpdate();
    },
    resetFilter: function () {
      this.filter = "";
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
      }, 3000);
    },
    saveConfig(configuration) {
      var self = this;
      this.toggleEddit(configuration);
      if (configuration.config_data != "boolean") {
        let form = new VueForm({
          field: {
            value: configuration.config_value,
            required: true,
            type: configuration.config_data.validate_as,
            maxLength: configuration.config_data.max_lenght,
            minLength: configuration.config_data.min_lenght,
          },
        });
        form.validate();
        if (form.errors.length > 0) {
          configuration.validate = false;
          M.toast({ html: "Verificar la configuracion del campo" });
        } else {
          configuration.validate = true;
          this.runSave(configuration);
        }
      } else {
        this.runSave(configuration);
      }
    },
    runSave(configuration) {
      var self = this;
      var url = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: url,
        data: configuration,
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            if (typeof callBack == "function") {
              callBack(response);
            }
            M.toast({ html: "Config Saved!" });
          } else {
            M.toast({ html: response.responseJSON.error_message });
          }
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
        },
      });
    },
    getconfigurations: function () {
      var self = this;
      var url = BASEURL + "api/v1/config/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let configurations = response.data;
          for (const key in configurations) {
            if (configurations.hasOwnProperty(key)) {
              configurations[key].user = new User(configurations[key].user);
              try {
                configurations[key].config_data = JSON.parse(
                  configurations[key].config_data
                );
              } catch (error) {
                configurations[key].config_data = {};
              }
            }
          }
          self.configurations = configurations;
          self.loader = false;
        })
        .catch((response) => {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        });
    },
    deleteConfiguration: function (configuration, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/configuration/" + configuration.configuration_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.configurations.splice(index, 1);
          }
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
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getconfigurations();
    });
  },
});
