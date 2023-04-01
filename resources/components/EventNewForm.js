var EventNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    event_id: null,
    name: "",
    subtitle: "",
    address: "",
    content: "",
    status: false,
    visibility: true,
    date_publish: "",
    date_create: "",
    date_update: "",
    type: "event",
    categorie_id: "0",
    categories: [],
    categories: [],
    user_id: null,
    user: null,
    mainImage: [],
    publishondate: true,
    datepublish: "",
    timepublish: "",
  },
  computed: {
    btnEnable: function () {
      let enable = !!this.name || false;
      if (enable) {
        //this.autoSave();
      }
      return enable;
    },
    getDateTimePublish: function () {
      return this.datepublish && this.timepublish
        ? this.datepublish + " " + this.timepublish + ":00"
        : null;
    },
    getMainImagenPath() {
      if (this.mainImage.length > 0) {
        return this.mainImage[0].file_id;
      }
      return null;
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
    removeImage(index) {
      if (this.mainImage.length > 0) {
        this.mainImage.splice(index, 1);
      }
      if (this.mainImage.length == 0) {
        this.mainImage = [];
      }
    },
    getFileImagenPath(file) {
      return BASEURL + file.file_path.substr(2) + this.getFileImagenName(file);
    },
    getFileImagenName(file) {
      return file.file_name + "." + file.file_type;
    },
    copyCallcack(files) {
      let file = files[0];
      file = new ExplorerFile(file);
      this.mainImage.push(file);
      let instance = M.Modal.getInstance($("#fileUploader"));
      instance.close();
    },
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
      return this.name && this.content;
    },

    save() {
      var self = this;
      var callBack = (response) => {
        var toastHTML = "<span>Event saved </span>";
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
      this.debug ? console.log(`${getFuncName()} fired`) : null;
      var self = this;
      var url = BASEURL + "api/v1/events";
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
            self.event_id = response.data.event_id;
            if (typeof callBack == "function") {
              callBack(response);
            }
          } else {
            M.toast({ html: response.error_message });
            self.loader = false;
          }
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    getData: function () {
      return {
        event_id: this.event_id || "",
        name: this.name || "",
        subtitle: this.subtitle || "",
        content: this.content || "",
        address: this.address || "",
        mainImage: this.getMainImagenPath,
        categorie_id: this.categorie_id || 0,
        status: this.status ? 1 : 0,
        visibility: this.visibility ? 1 : 0,
        date_publish: this.getDateTimePublish,
        date_create: this.date_create,
        date_update: this.date_update,
      };
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.event_id == value.event_id;
      });
    },
    serverValidation(field) {
      var self = this;
      var url = BASEURL + "admin/usuarios/ajax_check_field";
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
      this.debug ? console.log(`${getFuncName()} fired`) : null;
      var self = this;
      var url = BASEURL + "api/v1/categorie/type/" + self.type;
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
      this.debug ? console.log(`${getFuncName()} fired`) : null;
      var self = this;
      if (event_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/events/" + event_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.event_id = response.data.event_id;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.date_update = response.data.date_update;
              self.name = response.data.name;
              self.subtitle = response.data.subtitle;
              self.categorie_id = response.data.categorie_id;
              self.status = response.data.status;
              self.visibility = response.data.visibility;
              self.address = response.data.address;
              self.type = response.data.type;
              self.user_id = response.data.user_id;
              self.parent = response.data.parent;
              self.subcategories = response.data.subcategories;
              self.user = new User(response.data.user);
              self.content = response.data.content;
              if (response.data.mainImage) {
                self.mainImage.push(new ExplorerFile(response.data.mainImage));
              }
              setTimeout(() => {
                tinymce.editors["id_cazary"].setContent(response.data.content);
              }, 5000);
            }
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
      this.debug ? console.log(`${getFuncName()} fired`) : null;
      var elems = document.querySelectorAll(".datepicker");
      M.Datepicker.init(elems, {
        format: "yyyy-mm-dd",
        onClose: function () {
          alert("Datepicker");
          EventNewForm.datepublish =
            document.getElementById("datepublish").value;
        },
      });
      var elems = document.querySelectorAll(".timepicker");
      M.Timepicker.init(elems, {
        twelveHour: false,
        defaultTime: "now",
        onCloseEnd: function () {
          alert("Timepicker");
          EventNewForm.timepublish =
            document.getElementById("timepublish").value;
        },
      });
      tinymce.init({
        selector: "textarea",
        plugins: ["link table code"],
        setup: (editor) => {
          editor.on("Change", (e) => {
            this.content = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted EventNewForm") : null;
      this.getCategories();
      this.initPlugins();
      this.checkEditMode();
    });
  },
});
