var PageNewForm = new Vue({
  el: "#root",
  data: {
    debug: true,
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
    templates: [],
    layout: "default",
    layouts: [],
    categorie: "0",
    subcategorie: "0",
    pageTypes: [],
    categories: [],
    subcategories: [],
    pageType: {
      page_type_id: "1",
      page_type_name: "page",
    },
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
    preview_link: function () {
      return this.page_id
        ? BASEURL + "admin/paginas/preview?page_id=" + this.page_id
        : "";
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
    pageType: function (newType, oldType) {
      this.debug ? console.log(newType, oldType) : null;
      if (this.path.indexOf(oldType.page_type_name + "/") != -1) {
        let remplace = newType.page_type_name + "/";
        if (newType.page_type_id == "1") {
          remplace = "";
        }
        this.path = this.path.replace(oldType.page_type_name + "/", remplace);
      }
      this.setPath(this.path);
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
    setPath(value) {
      let addSubPath = this.pageType && this.pageType.page_type_id != "1";
      if (
        addSubPath &&
        value.indexOf(this.pageType.page_type_name + "/") != -1
      ) {
        value = value.replace(this.pageType.page_type_name + "/", "");
      }
      let slug = this.string_to_slug(value);
      if (addSubPath) {
        this.path = this.pageType.page_type_name + "/" + slug;
      } else {
        this.path = slug;
      }
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
    validateForm() {
      this.form.validate();
      let errors = true;
      if (!this.publishondate && !this.datepublish && !this.timepublish) {
        debugger;
        error = false;
      }

      return this.form.errors.length == 0 && errors;
    },
    save() {
      var self = this;
      var callBack = function (response) {
        if (response.data.status == 1) {
          var toastHTML =
            '<span>Page saved </span><a target="_blank" href="' +
            BASEURL +
            response.data.path +
            '" class="btn-flat toast-action">View in site</a>';
        } else {
          var toastHTML =
            '<span>Draf saved </span><a target="_blank" href="' +
            BASEURL +
            "admin/paginas/preview?page_id=" +
            response.data.page_id +
            '" class="btn-flat toast-action">Preview</a>';
        }
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
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/pages",
        data: self.getData(),
        dataType: "json",
        success: function (response) {
          setTimeout(() => {
            self.loader = false;
          }, 1500);
          if (response.code == 200) {
            self.editMode = true;
            self.page_id = response.data.page_id;
            if (typeof callBack == "function") {
              callBack(response);
            }
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
    },
    getData: function () {
      return {
        title: this.form.fields.title.value || "",
        subtitle: this.form.fields.subtitle.value || "",
        path: this.path || "",
        type: this.pageType.page_type_id || 1,
        status: this.status ? 1 : 2,
        content: this.content || "",
        page_id: this.page_id || null,
        publishondate: this.publishondate,
        date_publish: this.getDateTimePublish,
        visibility: this.visibility,
        template: this.template || "default",
        layout: this.layout || "default",
        categorie: this.categorie || 0,
        subcategorie: this.subcategorie || 0,
      };
    },
    getTemplates() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/templates",
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log("ajax_get_templates: ", response) : null;
          if (response.code == 200) {
            self.templates = response.data.templates.map(function (value) {
              let template = value.split(".")[0];
              return template == "template" ? "default" : template;
            });
            self.layouts = response.data.layouts.map(function (value) {
              let layout = value.split(".")[0];
              return layout == "site" ? "default" : layout;
            });
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.loader = false;
        },
      });
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
    getPageTypes() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/types",
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log("ajax_get_page_types: ", response) : null;
          if (response.code == 200) {
            self.pageTypes = response.data;
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.loader = false;
        },
      });
    },
    getCategories() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/categorias/ajax_get_categorie_type",
        data: {
          categorie_type: "page",
        },
        dataType: "json",
        success: function (response) {
          self.debug
            ? console.log("ajax_get_categorie_type: ", response)
            : null;
          if (response.code == 200) {
            PageNewForm.categories = response.data;
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.loader = false;
        },
      });
    },
    getSubCategories() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/categorias/ajax_get_subcategorie_type",
        data: {
          categorie_type: "page",
          parent_id: self.categorie,
        },
        dataType: "json",
        success: function (response) {
          console.log("ajax_get_subcategorie_type: ", response);
          if (response.code == 200) {
            self.subcategories = response.data;
            PageNewForm.initSelects();
          }
        },
        error: function (error) {
          self.debug ? console.log(error) : null;
          self.loader = false;
        },
      });
    },
    checkEditMode() {
      var page_id = document.getElementById("page_id").value;
      var editMode = document.getElementById("editMode").value;
      if (page_id && editMode == "edit") {
        var self = this;
        self.editMode = true;
        $.ajax({
          type: "GET",
          url: BASEURL + "api/v1/pages/" + page_id,
          data: {},
          dataType: "json",
          success: function (response) {
            self.loader = false;
            self.debug ? console.log("ajax_get_page: ", response) : null;
            if (response.code == 200) {
              self.form.fields.title.value = response.data.title;
              self.form.fields.subtitle.value = response.data.subtitle;
              self.page_id = response.data.page_id;
              self.status = response.data.status == "1";
              self.path = response.data.path;
              self.visibility = response.data.visibility;
              self.publishondate = !!response.data.date_publish;
              self.template = response.data.template;
              self.categorie = response.data.categorie || 0;
              self.subcategories = response.data.subcategories || 0;
              setTimeout(() => {
                tinymce.editors["id_cazary"].setContent(response.data.content);
                self.content = response.data.content;
              }, 2000);
            }
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
    initPlugins() {
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
        this.initSelects();
      }, 1000);
    },
    initSelects() {
      setTimeout(() => {
        var elems = document.querySelectorAll("select");
        var instances = M.FormSelect.init(elems, {});
      }, 1000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted PageNewForm") : null;
      this.loader = false;
      this.getPageTypes();
      this.checkEditMode();
      this.getCategories();
      this.getTemplates();
      this.initPlugins();
    });
  },
});
