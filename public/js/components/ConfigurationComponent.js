Vue.component("configuration", {
  template: "#configurationComponent-template",
  props: ["configuration"],
  data: function () {
    return {
      show_body: true,
      show_arrow: false,
      show_label: true,
      handle_value_as: "input",
      last_value: "",
    };
  },
  mixins: [mixins],
  computed: {
    isChecked() {
      return (this.configuration.config_value == this.configuration.config_data.true);
    },
  },
  watch: {
    configuration: {
      handler: function (val) {
        this.initComponent();
      },
      deep: true,
    },
  },
  methods: {
    initComponent: function () {
      if (!this.configuration) return;
      if (!this.configuration.config_data) {
        this.configuration.config_data = {};
      }

      this.configuration.validate = true;
      let data = this.configuration.config_data;

      // Sanitize perm_values: ensure it's either a valid object/array or null
      if (data.perm_values && (typeof data.perm_values !== "object" || (Array.isArray(data.perm_values) && data.perm_values.length === 0))) {
        data.perm_values = null;
      } else if (data.perm_values === "") {
        data.perm_values = null;
      }

      // Automatic Switch Detection: If there are exactly 2 options, treat as switch
      if (Array.isArray(data.perm_values) && data.perm_values.length == 2 && !data.handle_as) {
        data.handle_as = "switch";
        // If "true" value is not defined, assume the last one is the positive one
        if (!data.true) {
          data.true = data.perm_values[1];
        }
      }

      // Default editor
      this.handle_value_as = data.handle_as || (data.perm_values ? "select" : "input");

      // Logic for Booleans / Switches
      if (data.type_value == "boolean" || this.handle_value_as == "switch") {
        this.show_body = false;
        this.show_arrow = false;
        this.show_label = false;
        this.handle_value_as = "switch";

        // Asegurar valores para el switch si no existen o son solo números
        if (!data.perm_values || (Array.isArray(data.perm_values) && data.perm_values[0] == '0' && data.perm_values[1] == '1')) {
          data.perm_values = ['No', 'Si'];
          data.false = '0';
          data.true = '1';
        }
      } else if (data.perm_values) {
        // Has options but not a switch
        this.show_body = true;
        this.show_arrow = true;
        this.show_label = false;
        this.handle_value_as = data.handle_as || "select";
      } else {
        // Standard input
        this.show_body = true;
        this.show_label = true;
        this.show_arrow = false;
        this.handle_value_as = data.handle_as || "input";
      }
    },
    toggleEddit() {
      this.configuration.editable = !this.configuration.editable;
      this.$forceUpdate();
    },
    switchCahnged($event) {
      let isChecked = $event.target.checked;
      let data = this.configuration.config_data;
      if (isChecked) {
        this.configuration.config_value = data.true || (data.perm_values ? data.perm_values[1] : '1');
      } else {
        this.configuration.config_value = data.false || (data.perm_values ? data.perm_values[0] : '0');
      }
      this.runSave();
    },

    focusInput() {
      this.last_value = JSON.stringify({
        value: this.configuration.config_value,
        label: this.configuration.config_label,
      });
    },
    saveConfig() {
      var self = this;
      let configuration = self.configuration;

      // Check if anything actually changed
      let current_state = JSON.stringify({
        value: configuration.config_value,
        label: configuration.config_label,
      });

      if (current_state === this.last_value) {
        if (configuration.editable) {
          this.toggleEddit();
        }
        return;
      }

      if (configuration.editable) {
        this.toggleEddit();
      }

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
        let data = configuration.config_data;
        if (configuration.config_value) {
          configuration.config_value = data.true || (data.perm_values ? data.perm_values[1] : '1');
        } else {
          configuration.config_value = data.false || (data.perm_values ? data.perm_values[0] : '0');
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
            M.toast({ html: response.error_message });
          }
        },
        error: function (response) {
          M.toast({ html: response.error_message });
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
      this.initComponent();
    });
  },
});
