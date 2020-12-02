Vue.component("configuration", {
  template: "#configurationComponent-template",
  props: ["configuration"],
  data: function () {
    return {
      show_body: true,
      show_arrow: false,
      show_label: true,
      handle_value_as: "input",
    };
  },
  mixins: [mixins],
  computed: {
    isChecked() {
      return (this.configuration.config_value == this.configuration.config_data.true);
    },
  },
  methods: {
    toggleEddit() {
      this.configuration.editable = !this.configuration.editable;
      this.$forceUpdate();
    },
    switchCahnged($event) {
      let isChecked = $event.target.checked;
      if (isChecked) {
        this.configuration.config_value = this.configuration.config_data.true;
      } else {
        this.configuration.config_value = this.configuration.config_data.true;
        this.configuration.config_data.perm_values.forEach((element) => {
          if (element != this.configuration.config_data.true) {
            this.configuration.config_value = element;
          }
        });
      }
      this.runSave();
    },

    saveConfig() {
      var self = this;
      this.toggleEddit();
      let configuration = self.configuration;
      if (configuration.config_data.type_value != "boolean") {
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
          this.runSave();
        }
      } else {
        if (configuration.config_value) {
          configuration.config_value = configuration.config_data.true;
        } else {
          configuration.config_data.perm_values.forEach((element) => {
            if (element != configuration.config_data.true) {
              configuration.config_value = element;
            }
          });
        }
        this.runSave();
      }
    },
    runSave() {
      var self = this;
      var url = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: url,
        data: this.configuration,
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
  },
  mounted: function () {
    this.$nextTick(function () {
      this.initPlugins();
      this.configuration.validate = true;
      this.handle_value_as = this.configuration.config_data.handle_as;
      if (this.configuration.config_data.type_value == "boolean") {
        this.show_body = false;
        this.show_arrow = false;
        this.show_label = false;
        this.handle_value_as = "switch";
      } else if (
        this.configuration.config_data.type_value == "string" &&
        this.configuration.config_data.perm_values == null
      ) {
        this.show_body = true;
        this.show_label = true;
        this.show_arrow = false;
        this.handle_value_as = this.configuration.config_data.handle_as;
      } else if (
        this.configuration.config_data.type_value == "string" &&
        typeof this.configuration.config_data.perm_values == "object"
      ) {
        this.show_body = true;
        this.show_arrow = true;
        this.show_label = false;
        this.handle_value_as = this.configuration.config_data.handle_as;
      }
    });
  },
});
