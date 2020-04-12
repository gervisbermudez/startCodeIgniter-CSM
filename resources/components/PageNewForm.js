var PageNewForm = new Vue({
  el: "#root",
  data: {
    loader: true,
    editMode: false,
    page_id: null,
    form: new VueForm({
      title: {
        value: null,
        required: true,
        type: "username",
        maxLength: 120,
        minLength: 5,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
      subtitle: {
        value: null,
        required: false,
        type: "username",
        maxLength: 120,
        minLength: 5,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
    }),
    status: false,
    path: "",
    content: "",
    visibility: 1,
    publishondate: true,
    datepublish: "",
    timepublish: "",
    template: "default",
    categorie: 1,
    subcategories: 1,
  },
  computed: {
    btnEnable: function () {
      let enable =
        (!!this.form.fields.title.value && !!this.path && !!this.content) ||
        false;
      if (enable) {
        this.autoSave();
      }
      return enable;
    },
    getDateTimePublish: function () {
      return this.datepublish && this.timepublish
        ? this.datepublish + " " + this.timepublish + ":00"
        : null;
    },
  },
  watch: {
    "form.fields.title.value": function (value) {
      this.setPath(value);
    },
    publishondate: function (value) {
      if (value) {
        this.datepublish = "";
        this.timepublish = "";
      }
    },
  },
  methods: {
    autoSave() {
      //@todo
      console.log("running autosave...");
    },
    setPath(value) {
      this.path = this.string_to_slug(value);
    },
    string_to_slug: function (str) {
      if (str.length == 0) return "";

      str = str.replace(/^\s+|\s+$/g, ""); // trim
      str = str.toLowerCase();

      // remove accents, swap ñ for n, etc
      var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
      var to = "aaaaeeeeiiiioooouuuunc------";
      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
      }

      str = str
        .replace(/[^a-z0-9 -]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

      return str;
    },
    validateField(field) {
      let self = UserNewForm;
      if (self.form.validateField(field)) {
        self.serverValidation(field);
        return;
      }
      return self.form.fields[field].valid;
    },
    save() {
      var self = this;
      self.form.validate();
      if (self.form.errors.length == 0) {
        this.loader = true;
        $.ajax({
          type: "POST",
          url: BASEURL + "admin/paginas/ajax_save_page",
          data: self.getData(),
          dataType: "json",
          success: function (response) {
            if (response.code == 200) {
              console.log(response);
              self.loader = false;
            } else {
              M.toast({ html: response.responseJSON.error_message });
              self.loader = false;
            }
          },
          error: function (response) {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          },
        });
      } else {
        M.toast({ html: "Verifique todos los campos del formulario" });
      }
    },
    getData: function () {
      return {
        title: this.form.fields.title.value || "",
        subtitle: this.form.fields.subtitle.value || "",
        path: this.path || "",
        status: this.status ? 1 : 0,
        content: this.content || "",
        page_id: this.page_id,
        publishondate: this.publishondate,
        date_publish: this.getDateTimePublish,
        template: this.template,
        visibility: this.visibility,
        template: this.template,
        categorie: this.categorie,
        subcategories: this.subcategories,
      };
    },
    serverValidation(field) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_check_field",
        data: {
          field: field,
          value: self.form.fields[field].value,
        },
        dataType: "json",
        success: function (response) {
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
    getUsergroups() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/usuarios/ajax_get_usergroups",
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.usergroups = response.data;
            setTimeout(() => {
              var elems = document.querySelectorAll("select");
              var instances = M.FormSelect.init(elems, {});
            }, 2000);
            self.loader = false;
          }
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    checkEditMode() {
      if (user_id) {
        var self = this;
        self.editMode = true;
        $.ajax({
          type: "POST",
          url: BASEURL + "admin/usuarios/ajax_get_users",
          data: {
            user_id: user_id,
          },
          dataType: "json",
          success: function (response) {
            if (response.code == 200) {
              let data = response.data[0];
              self.user_id = data.user_id;
              self.form.fields.username.value = data.username;
              self.form.fields.email.value = data.email;
              self.status = data.status;
              self.form.fields.usergroup_id.value = data.usergroup_id;
              self.form.fields.nombre.value = data.user_data.nombre;
              self.form.fields.apellido.value = data.user_data.apellido;
              self.form.fields.direccion.value = data.user_data.direccion;
              self.form.fields.telefono.value = data.user_data.telefono;
            }
            self.loader = false;
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          },
          error: function (error) {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          },
        });
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("mounted PageNewForm");
      this.loader = false;
      tinymce.init({
        selector: "textarea",
        plugins: ["link table code"],
        setup: function (editor) {
          editor.on("Change", function (e) {
            PageNewForm.content = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
      setTimeout(() => {
        var elems = document.querySelectorAll(".datepicker");
        M.Datepicker.init(elems, {
          format: "yyyy-mm-dd",
          onClose: function () {
            PageNewForm.datepublish = document.getElementById(
              "datepublish"
            ).value;
          },
        });
        var elems = document.querySelectorAll(".timepicker");
        M.Timepicker.init(elems, {
          twelveHour: false,
          defaultTime: "now",
          onCloseEnd: function () {
            PageNewForm.timepublish = document.getElementById(
              "timepublish"
            ).value;
          },
        });
      }, 1000);
    });
  },
});
