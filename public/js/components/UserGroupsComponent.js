var UserGroupsComponents = new Vue({
  el: "#root",
  data: {
    usergroups: [],
    currentUsergroups: [],
    tableView: false,
    loader: true,
    filter: "",
    orderDataConf: {
      strPropertyName: null,
      sort_as: "asc",
    },
  },
  mixins: [mixins],
  computed: {
    filterUsergroups: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.usergroups.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.usergroups;
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
            M.toast({ html: response.error_message });
          }
        },
        error: function (response) {
          M.toast({ html: response.error_message });
        },
      });
    },
    getUserGroups: function () {
      var self = this;
      var url = BASEURL + "api/v1/users/usergroups/";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let usergroups = response.data;
          usergroups.map((element) => {
            if (element.user) {
              element.user = new User(element.user);
            } else {
              element.user = new User({});
            }
            return element;
          });
          self.usergroups = usergroups;
          self.loader = false;
          this.initPlugins();
        })
        .catch((response) => {
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
            self.usergroups.splice(index, 1);
          }
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
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
      this.getUserGroups();
    });
  },
});
