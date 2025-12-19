var AlbumNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    album_id: null,
    items: [],
    form: new VueForm({
      name: {
        value: null,
        required: true,
        type: "address",
        maxLength: 250,
        minLength: 1,
        customPattern: /[a-zA-Z0-9,#.-\s]+/,
      },
    }),
    description: "",
    content: "",
    status: false,
    date_publish: "",
    date_create: "",
    date_update: "",
    user_id: null,
    user: null,
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      let enable =
        (!!this.form.fields.name.value && !!this.description) || false;
      if (enable) {
        //this.autoSave();
      }
      return enable;
    },
    preselected: function () {
      return this.items.map((item) => {
        return item.file;
      });
    },
  },
  methods: {
    getPageImagePath(item) {
      if (item.file.get_full_file_path()) {
        return item.file.get_full_file_path();
      }
      return BASEURL + "/public/img/default.jpg";
    },
    copyCallcack(selectedFiles) {
      let instance = M.Modal.getInstance($(".modal"));
      instance.close();
      this.items = [
        ...selectedFiles.map((item) => {
          let album_item = {
            album_id: "",
            album_item_id: "",
            date_create: "",
            date_update: "",
            description: "",
            file: new ExplorerFile(item),
            file_id: item.file_id,
            name: "",
            status: "1",
          };

          this.items.forEach((element) => {
            debugger;
            if (element.file.file_id == item.file_id) {
              album_item = element;
            }
          });

          return album_item;
        }),
      ];

      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
      }, 3000);
    },
    autoSave() {
      if (!this.status) {
        this.runSaveData((response) => {
          console.log(response);
        });
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
        var toastHTML = `<span>Album saved </span><a href="${
          BASEURL + "admin/gallery/items/" + self.album_id
        }" class="btn-flat toast-action">View</a>`;
        M.toast({ html: toastHTML });
      };
      if (self.validateForm()) {
        this.loader = true;
        this.runSaveData(callBack);
      } else {
        M.toast({ html: "Verifique todos los campos del formulario" });
      }
    },
    removeItemImage(index) {
      var index = index;
      var self = this;
      let item = this.items[index];
      if (item.album_item_id) {
        $.ajax({
          type: "GET",
          url:
            BASEURL + "api/v1/albumes/delete_album_item/" + item.album_item_id,
          data: {},
          dataType: "json",
          success: function (response) {
            if (response.code == 200) {
              M.toast({ html: "Imagen quitada" });
              self.items.splice(index, 1);
            }
          },
        });
      } else {
        this.items.splice(index, 1);
      }
    },
    runSaveData(callBack) {
      var self = this;
      var url = BASEURL + "api/v1/albumes";
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
            self.album_id = response.data.album_id;
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
        album_id: this.album_id || "",
        name: this.form.fields.name.value || "",
        description: this.description || "",
        status: this.status ? 1 : 0,
        date_publish: this.date_publish,
        date_create: this.date_create,
        date_update: this.date_update,
        album_items: this.items,
      };
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.album_id == value.album_id;
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
    checkEditMode() {
      var self = this;
      if (album_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/albumes/" + album_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.album_id = response.data.album_id;
              self.form.fields.name.value = response.data.name;
              self.date_create = response.data.date_create;
              self.date_publish = response.data.date_publish;
              self.date_update = response.data.date_update;
              self.description = response.data.description;
              self.status = response.data.status == "0" ? false : true;
              self.user_id = response.data.user_id;
              self.items = response.data.items.map((item) => {
                item.file = new ExplorerFile(item.file);
                return item;
              });
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
            this.debug ? console.log("tinymce change fired") : null;
            this.description = tinymce.editors["id_cazary"].getContent();
          });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted AlbumNewForm") : null;
      this.checkEditMode();
      this.initPlugins();
    });
  },
});
