var CategoriaNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    categorie_id: null,
    form: new VueForm({
      name: {
        value: null,
        required: true,
        type: "username",
        maxLength: 120,
        minLength: 1,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
    }),
    description: "",
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    type: "page",
    parent_id: "0",
    categories: [],
    parent: null,
    subcategories: [],
    categories_type: [
      "page",
      "formulario",
      "video",
      "foto",
      "evento",
      "contenido",
    ],
    user_id: null,
    user: null,
  },
  computed: {
    btnEnable: function () {
      let enable = !!this.form.fields.name.value || false;
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
        var toastHTML = "<span>Category saved </span>";
        M.toast({ html: toastHTML });
      };
      if (self.validateForm()) {
        this.loader = true;
        this.runSaveData(callBack);
      } else {
        M.toast({ html: "Check all form fields" });
      }
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/categories";
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
            self.categorie_id = response.data.categorie_id;
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
          M.toast({ html: "An unexpected error occurred" });
          console.error(error);
        },
      });
    },
    getData: function () {
      return {
        categorie_id: this.categorie_id || "",
        name: this.form.fields.name.value || "",
        description: this.description || "",
        type: this.type || "",
        parent_id: this.parent_id || 0,
        status: this.status ? 1 : 0,
        date_publish: this.date_publish,
        date_create: this.date_create,
        date_update: this.date_update,
      };
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.categorie_id == value.categorie_id;
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
    getCategories() {
      var self = this;
      var url = BASEURL + "api/v1/categories/type/" + self.type;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          self.loader = false;
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.categories = response.data;
            this.initSelects();
          }
        })
        .catch((err) => {
          self.debug ? console.log(err) : null;
          self.loader = false;
        });
    },
    checkEditMode() {
      var self = this;
      if (categorie_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/categories/" + categorie_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.categorie_id = response.data.categorie_id;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.date_update = response.data.date_update;
              self.description = response.data.description;
              self.form.fields.name.value = response.data.name;
              self.parent_id = response.data.parent_id;
              self.status = response.data.status;
              self.type = response.data.type;
              self.user_id = response.data.user_id;
              self.parent = response.data.parent;
              self.subcategories = response.data.subcategories;
              self.user = new User(response.data.user);
              setTimeout(() => {
                tinymce.editors["id_cazary"].setContent(self.description);
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
        base_url: BASEURL + '/public/vendors/tinymce/js/tinymce',
        selector: "textarea",
        plugins: ["link table code"],
        setup: (editor) => {
          editor.on("Change", (e) => {
            this.description = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted CategoriaNewForm") : null;
      this.getCategories();
      this.initPlugins();
      this.checkEditMode();
    });
  },
});
