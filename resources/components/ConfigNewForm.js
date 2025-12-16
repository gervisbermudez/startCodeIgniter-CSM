var ConfigNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    site_config_id: null,
    user_id: null,
    user: null,
    form: new VueForm({
      config_name: {
        value: null,
        required: true,
        type: "username",
        maxLength: 120,
        minLength: 1,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
    }),
    config_description: "",
    config_value: "",
    config_label: "",
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    type: "general",
    parent_id: "0",
    site_configs: [],
    parent: null,
    subsite_configs: [],
    config_type: "general",
    config_type_options: [
      "general",
      "analytics",
      "logger",
      "pixel",
      "seo",
      "theme",
      "updater",
    ],
    type_value: "string",
    validate_as: "text",
    max_lenght: "50",
    min_lenght: "5",
    handle_as: "input",
    input_type: "text",
    type_values_options: ["string", "number", "boolean"],
    handle_as_options: ["input", "switch"],
    input_type_options: [
      "text",
      "textarea",
      "select",
      "button",
      "switch",
      "checkbox",
      "color",
      "date",
      "datetime-local",
      "email",
      "file",
      "hidden",
      "image",
      "month",
      "number",
      "password",
      "radio",
      "range",
      "reset",
      "search",
      "submit",
      "tel",
      "time",
      "url",
      "week",
    ],
    readonly: false,
    true_value: "",
  },
  computed: {
    btnEnable: function () {
      let enable =
        (!!this.form.fields.config_name.value && !!this.config_value) || false;
      if (enable) {
        //this.autoSave();
      }
      return enable;
    },
  },
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    autoSave() {
      if (!this.status) {
        this.runSaveData();
        this.debug ? console.log("running autosave...") : null;
      }
    },
    validateField(field) {
      let self = UserNewForm;
      if (self.form.validateField(field)) {
        self.serverValidation(field);
        return;
      }
      return self.form.fields[field].valid;
    },
    validateForm() {
      this.form.validate();
      let errors = true;
      return this.form.errors.length == 0 && errors;
    },
    save() {
      var self = this;
      var callBack = (response) => {
        var toastHTML = "<span>Config saved </span>";
        M.toast({ html: toastHTML });
      };
      if (self.validateForm()) {
        this.loader = true;
        this.runSaveData(callBack);
      } else {
        M.toast({ html: "Verifique todos los campos del formulario" });
      }
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/config";
      $.ajax({
        type: "POST",
        url: url,
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          setTimeout(() => {
            self.loader = false;
          }, 1500);
          if (response.code == 200) {
            self.editMode = true;
            self.site_config_id = response.data.site_config_id;
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: response.error_message });
            self.loader = false;
          }
        },
        error: function (response) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    getData: function () {
      return {
        site_config_id: this.site_config_id || "",
        config_value: this.config_value || "",
        config_label: this.config_label || this.form.fields.config_name.value,
        config_name:
          this.form.fields.config_name.value
            .toUpperCase()
            .split(" ")
            .join("_") || "",
        config_description: this.config_description || "",
        config_type: this.config_type || "",
        status: this.status ? 1 : 0,
        readonly: this.readonly ? 1 : 0,
        date_publish: this.date_publish,
        date_create: this.date_create,
        date_update: this.date_update,
        config_data: {
          type_value: this.type_value,
          validate_as: this.validate_as,
          max_lenght: this.max_lenght,
          min_lenght: this.min_lenght,
          handle_as: this.handle_as,
          input_type: this.input_type,
          perm_values: this.getTags(),
          true: this.true_value,
        },
      };
    },
    getSelectedCategorie() {
      return this.site_configs.filter((value, index) => {
        return this.site_config_id == value.site_config_id;
      });
    },
    serverValidation(field) {
      var self = this;
      var url = BASEURL + "admin/users/ajax_check_field";
      $.ajax({
        type: "POST",
        url: url,
        data: {
          field: field,
          value: self.form.fields[field].value,
        },
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.code) {
            self.form.fields[field].valid = response.data;
            if (response.data) {
              self.form.markFieldAsValid(field);
            } else {
              self.form.fields[field].errorText =
                "The " + field + " is already registered";
            }
            self.$forceUpdate();
          }
        },
      });
    },
    onChangeTypeValue() {
      switch (this.type_value) {
        case "string":
          this.validate_as = "text";
          this.handle_as = "input";
          this.input_type = "text";
          break;

        case "number":
          this.validate_as = "number";
          this.handle_as = "input";
          this.input_type = "number";
          break;

        case "boolean":
          this.validate_as = "boolean";
          this.handle_as = "switch";
          this.input_type = "switch";
          break;

        default:
          this.validate_as = "text";
          this.handle_as = "text";
          break;
      }
    },
    checkEditMode() {
      var self = this;
      if (site_config_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/config/" + site_config_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.site_config_id = response.data.site_config_id;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.date_update = response.data.date_update;
              self.config_description = response.data.config_description;
              self.form.fields.config_name.value = response.data.config_name;
              self.status = response.data.status;
              self.config_type = response.data.config_type;
              self.user_id = response.data.user_id;
              self.user = new User(response.data.user);
              setTimeout(() => {
                tinymce.editors["id_cazary"].setContent(
                  self.config_description
                );
              }, 5000);
            }
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          })
          .catch((response) => {
            M.toast({ html: response.error_message });
            self.loader = false;
          });
      } else {
        self.loader = false;
      }
    },
    initSelects() {
      setTimeout(() => {
        var elems = document.querySelectorAll("select");
        var instances = M.FormSelect.init(elems, {});
      }, 1000);
    },
    initPlugins() {
      tinymce.init({
        selector: "textarea",
        plugins: ["link table code"],
        setup: (editor) => {
          editor.on("Change", (e) => {
            this.description = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
      M.Chips.init(document.getElementById("tags"), {
        placeholder: "Enter a value",
      });

      setTimeout(() => {
        const instance = M.Chips.getInstance(document.getElementById("tags"));
        instance.options.onChipAdd = (value) => {
          let tags = this.getTags();
          if (this.config_value == "") {
            this.config_value = tags[0];
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          }
        };
      }, 2000);
    },
    getTags() {
      let tags = [];
      const instance = M.Chips.getInstance(
        document.getElementById("tags")
      ).chipsData;
      instance.forEach((element) => {
        tags.push(element.tag.trim().toUpperCase());
      });
      setTimeout(() => {
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
        this.initMaterialboxed();
      }, 1000);
      return tags;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted ConfigNewForm") : null;
      this.initPlugins();
      this.checkEditMode();
    });
  },
});
