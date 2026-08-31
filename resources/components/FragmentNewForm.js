var FragmentNewForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    fragment_id: null,
    form: new VueForm({
      name: {
        value: null,
        required: true,
        type: "text",
        maxLength: 120,
        minLength: 1,
        customPattern: /^[a-zA-Z0-9_\-\s]+$/,
      },
    }),
    description: "",
    status: false,
    date_create: "",
    date_update: "",
    type: "contenido",
    fragment_types: [
      "contenido",
      "parrafo",
      "widget",
      "page",
      "formulario",
      "video",
      "foto",
      "evento",
    ],
    user_id: null,
    user: null,
    formEndpoint: "api/v1/fragments/",
    formIdField: "fragment_id",
  },
  mixins: [mixins, formMixin],
  computed: {
    btnEnable: function () {
      return !!this.form.fields.name.value;
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
    validateForm() {
      this.form.validate();
      return this.form.errors.length == 0;
    },
    save() {
      var self = this;
      if (typeof tinymce !== "undefined" && tinymce.get("id_cazary")) {
        this.description = tinymce.get("id_cazary").getContent();
      }
      if (self.validateForm()) {
        this.loader = true;
        this.runSaveData(function () {
          self.toast("toast_saved");
        });
      } else {
        this.toast("toast_form_invalid");
      }
    },
    getData: function () {
      return {
        fragment_id: this.fragment_id || "",
        name: this.form.fields.name.value || "",
        description: this.description || "",
        type: this.type || "",
        status: this.status ? 1 : 2,
      };
    },
    setEditorContent: function (html) {
      if (typeof tinymce === "undefined") {
        return;
      }
      var editor = tinymce.get("id_cazary");
      if (editor) {
        editor.setContent(html || "");
      }
    },
    checkEditMode() {
      var self = this;
      if (fragment_id && editMode == "edit") {
        self.editMode = true;
        var url = BASEURL + "api/v1/fragments/" + fragment_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.fragment_id = response.data.fragment_id;
              self.date_create = response.data.date_create;
              self.date_update = response.data.date_update;
              self.description = response.data.description;
              self.form.fields.name.value = response.data.name;
              self.status =
                response.data.status == 1 || response.data.status == "1";
              self.type = response.data.type;
              self.user_id = response.data.user_id;
              self.user = new User(response.data.user);
              self.setEditorContent(self.description);
              self.$nextTick(function () {
                self.initSelects();
                M.updateTextFields();
              });
            }
          })
          .catch((response) => {
            M.toast({ html: response.error_message });
            self.loader = false;
          });
      } else {
        self.loader = false;
        self.$nextTick(function () {
          self.initSelects();
        });
      }
    },
    initSelects() {
      var elems = document.querySelectorAll("select");
      M.FormSelect.init(elems, {});
    },
    initPlugins() {
      var self = this;
      tinymce.init({
        base_url: BASEURL + "/public/vendors/tinymce/js/tinymce",
        selector: "#id_cazary",
        plugins: ["link table code"],
        setup: function (editor) {
          editor.on("Change", function () {
            self.description = editor.getContent();
          });
          editor.on("init", function () {
            if (self.description) {
              editor.setContent(self.description);
            }
          });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.initPlugins();
      this.checkEditMode();
    });
  },
});
