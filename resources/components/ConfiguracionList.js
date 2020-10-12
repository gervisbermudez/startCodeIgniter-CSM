var ConfiguracionList = new Vue({
  el: "#root",
  data: {
    configurations: [],
    tableView: false,
    loader: true,
    filter: "",
    sectionActive: "home",
    routes: ["home", "analytics", "seo", "pixel"],
  },
  mixins: [mixins],
  computed: {
    seoConfigurations: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.configurations.filter((value, index) => {
          return (
            this.searchInObject(value, filterTerm) && value.config_type == "seo"
          );
        });
      } else {
        return this.filterConfigurations("seo");
      }
    },
    generalConfigurations: function () {
      return this.filterConfigurations("general");
    },
    analyticsConfigurations: function () {
      return this.filterConfigurations("analytics");
    },
    seoConfigurations: function () {
      return this.filterConfigurations("seo");
    },
    pixelConfigurations: function () {
      return this.filterConfigurations("pixel");
    },
  },
  methods: {
    getConfig: function (value, prop = "config_name") {
      let config_object = null;
      this.configurations.forEach((config) => {
        if (config[prop] == value) {
          config_object = config;
        }
      });
      return config_object;
    },
    getConfigValue: function (value, prop = "config_name") {
      let config_object = this.getConfig(value, prop);
      return config_object ? config_object.config_value : "";
    },
    getConfigValueBoolean: function (value, prop = "config_name") {
      let config_object = this.getConfig(value, prop);
      return config_object && config_object.config_value ==  config_object.config_data.true;
    },
    updateConfig(e, prop = "config_name") {
      value = e.target.value;
      config = this.getConfig(prop);
      config.config_value = value;
      this.runSave(config);
    },
    updateConfigCheckbox(e, prop = "ANALYTICS_ACTIVE") {
      value = e.target.checked;
      config = this.getConfig(prop);

      if (value) {
        config.config_data.perm_values.forEach(element => {
          if (element  == config.config_data.true) {
            value = element
          }
        });
      } else {
        config.config_data.perm_values.forEach(element => {
          if (element  != config.config_data.true) {
            value = element
          }
        });
      }

      config.config_value = value;
      this.runSave(config);
    },
    filterConfigurations: function (config_type) {
      return this.configurations.filter((value, index) => {
        return value.config_type == config_type;
      });
    },
    changeSectionActive: function (section) {
      this.sectionActive = section;
      var newurl =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?section=" +
        section;
      window.history.pushState({ path: newurl }, "", newurl);
    },
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
      window.addEventListener(
        "popstate",
        (event) => {
          try {
            var search = location.search.substring(1);
            var search = JSON.parse(
              '{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
              function (key, value) {
                return key === "" ? value : decodeURIComponent(value);
              }
            );

            if (search.section && this.routes.includes(search.section)) {
              this.sectionActive = search.section;
            } else {
              this.sectionActive = "home";
            }
          } catch (e) {
            this.sectionActive = "home";
          }
        },
        false
      );

      try {
        var search = location.search.substring(1);
        var search = JSON.parse(
          '{"' + search.replace(/&/g, '","').replace(/=/g, '":"') + '"}',
          function (key, value) {
            return key === "" ? value : decodeURIComponent(value);
          }
        );

        if (search.section && this.routes.includes(search.section)) {
          this.sectionActive = search.section;
        }
      } catch (e) {
        this.sectionActive = "home";
      }
    });
  },
});
