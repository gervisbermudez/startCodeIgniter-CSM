var runningAutoSave = false;

var PageNewForm = new Vue({
  el: "#PageNewForm-root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    editMode: false,
    page_id: null,
    user: null,
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
    layout: "default",
    categorie_id: "0",
    subcategorie_id: "0",
    page_type_id: "1",
    layouts: [],
    mainImage: [],
    templates: [],
    pageTypes: [],
    categories: [],
    subcategories: [],
    page_data: {
      title: "",
    },
    customMetas: [],
    metas: [
      {
        name: "author",
        content: new User(
          JSON.parse(localStorage.getItem("userdata"))
        ).get_fullname(),
      },
      {
        name: "keywords",
        content: "",
      },
      {
        name: "description",
        content: "",
      },
      {
        name: "ROBOTS",
        content: "NOODP",
      },
      {
        name: "GOOGLEBOT",
        content: "INDEX, FOLLOW",
      },
      {
        property: "og:title",
        content: "",
      },
      {
        property: "og:description",
        content: "",
      },
      {
        property: "og:site_name",
        content: SITE_TITLE,
      },
      {
        property: "og:url",
        content: "",
      },
      {
        property: "og:image",
        content: "",
      },
      {
        property: "og:type",
        content: "article",
      },
      {
        property: "twitter:card",
        content: "summary",
      },
      {
        property: "twitter:title",
        content: "",
      },
      {
        property: "twitter:description",
        content: "",
      },
      {
        property: "twitter:image",
        content: "",
      },
    ],
  },
  mixins: [mixins],
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
    getMainImagenPath() {
      if (this.mainImage.length > 0) {
        return this.mainImage[0].file_id;
      }
      return null;
    },
    getPagePath() {
      let segments = this.getPathSegments().filter((value, index) => {
        return value.length > 0;
      });
      segments = segments.map((value, index) => {
        return this.string_to_slug(value);
      });
      return segments.join("/");
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
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  methods: {
    addCustomMeta() {
      this.customMetas.push({
        name: "",
        content: "",
      });
    },
    removeMeta(index, is_custom = true) {
      if (is_custom) {
        this.customMetas.splice(index, 1);
      } else {
        this.metas.splice(index, 1);
      }
    },
    setMetaContent(strValue, strProperty, index) {
      if (index !== undefined) {
        this.metas[index].content = strValue;
        return;
      }
      this.metas = this.metas.map((meta) => {
        if (meta.property == strProperty || meta.name == strProperty) {
          meta.content = strValue;
        }
        return meta;
      });
    },
    getPathSegments() {
      /**
       * url path:
       * pageType / Categorie / SubCategorie / pagePath
       */
      let type = "";
      if (this.page_type_id == 1) {
        type = "";
      } else {
        type = this.getSelectedPageType();
        if (type.length) {
          type = type[0].page_type_name;
        } else {
          type = "";
        }
      }

      let categorie = this.getSelectedCategorie();
      categorie = categorie[0] ? categorie[0]["name"] : "";
      categorie = type == "" ? "" : categorie;
      let subcategorie = this.getSelectedSubCategorie();
      subcategorie = subcategorie[0] ? subcategorie[0]["name"] : "";
      subcategorie = type == "" ? "" : subcategorie;
      let pagePath = this.path;
      return [type, categorie, subcategorie, pagePath];
    },
    onChangeTitle(title) {
      this.page_data.title = title;
      this.setMetaContent(title, "og:title");
      this.setMetaContent(title, "twitter:title");
      this.setMetaContent(title, "keywords");
    },
    autoSave() {
      if (!this.status) {
        if (!runningAutoSave) {
          runningAutoSave = true;
          setTimeout(() => {
            this.runSaveData();
            this.debug ? console.log("running autosave...") : null;
            runningAutoSave = false;
          }, 3000);
        }
      }
    },
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
    setPath(value) {
      let slug = this.string_to_slug(value);
      this.path = slug;
      this.setMetaContent(BASEURL + slug, "og:url");
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
      var url = BASEURL + "api/v1/pages";
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
        path: this.getPagePath || "",
        page_type_id: this.page_type_id || 1,
        status: this.status ? 1 : 2,
        content: wp.data.select( "core/editor" ).getEditedPostContent() || "",
        page_id: this.page_id || null,
        publishondate: this.publishondate,
        date_publish: this.getDateTimePublish,
        visibility: this.visibility,
        template: this.template || "default",
        layout: this.layout || "default",
        categorie_id: this.categorie_id || 0,
        subcategorie_id: this.subcategorie_id || 0,
        mainImage: this.getMainImagenPath,
        page_data: {
          tags: this.getPageTags(),
          title: this.page_data.title,
          footer_includes: this.page_data.footer_includes,
          headers_includes: this.page_data.headers_includes,
          meta: [...this.metas, ...this.customMetas],
        },
      };
    },
    getPageTags() {
      let tags = [];
      const instance = M.Chips.getInstance(document.getElementById("pageTags"))
        .chipsData;
      instance.forEach((element) => {
        tags.push(element.tag);
      });
      return tags;
    },
    getTemplates() {
      var self = this;
      var url = BASEURL + "api/v1/pages/templates";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.loader = false;
          self.debug ? console.log(url, response) : null;
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
    getSelectedPageType() {
      return this.pageTypes.filter((value, index) => {
        return this.page_type_id == value.page_type_id;
      });
    },
    getSelectedCategorie() {
      return this.categories.filter((value, index) => {
        return this.categorie_id == value.categorie_id;
      });
    },
    getSelectedSubCategorie() {
      return this.subcategories.filter((value, index) => {
        return this.subcategorie_id == value.categorie_id;
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
    getPageTypes() {
      var self = this;
      var url = BASEURL + "api/v1/pages/types";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          self.loader = false;
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
      var url = BASEURL + "api/v1/categorie/type/page";
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.loader = false;
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            self.categories = response.data;
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
      var url = BASEURL + "api/v1/categorie/subcategorie/" + self.categorie_id;
      $.ajax({
        type: "GET",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          self.loader = false;
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
      localStorage.removeItem("g-editor-page");
      if (page_id && editMode == "edit") {
        var self = this;
        self.editMode = true;
        var url = BASEURL + "api/v1/pages/editpageinfo/" + page_id;
        fetch(url)
          .then((response) => response.json())
          .then((response) => {
            self.loader = false;
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.form.fields.title.value = response.data.page.title;
              self.form.fields.subtitle.value = response.data.page.subtitle;
              self.page_id = response.data.page.page_id;
              self.status = response.data.page.status == "1";
              self.path = response.data.page.path;
              self.visibility = response.data.page.visibility;
              self.publishondate = !!response.data.page.date_publish;
              self.template = response.data.page.template;
              self.categorie_id = response.data.page.categorie_id || 0;
              self.subcategories_id = response.data.page.subcategories_id || 0;
              self.pageTypes = response.data.page_types;
              self.page_type_id = response.data.page.page_type_id;
              self.page_data = response.data.page.page_data;
              const instance = M.Chips.getInstance(
                document.getElementById("pageTags")
              );
              self.page_data.tags
                ? self.page_data.tags.forEach((element) => {
                    instance.addChip({
                      tag: element,
                    });
                  })
                : null;
              response.data.page.page_data.meta
                ? (self.metas = response.data.page.page_data.meta)
                : null;
              self.user = new User(response.data.page.user);
              if (response.data.page.main_image) {
                self.mainImage.push(response.data.page.main_image);
              }
              self.templates = response.data.templates.map(function (value) {
                let template = value.split(".")[0];
                return template == "template" ? "default" : template;
              });
              self.layouts = response.data.layouts.map(function (value) {
                let layout = value.split(".")[0];
                return layout == "site" ? "default" : layout;
              });
              self.content = response.data.page;
              self.setEditorContent(response.data.page);
            }
            setTimeout(() => {
              M.updateTextFields();
            }, 1000);
          })
          .catch((response) => {
            M.toast({ html: response.responseJSON.error_message });
            self.loader = false;
          });
      } else {
        this.getPageTypes();
        this.getTemplates();
      }
    },
    copyCallcack(files) {
      let file = files[0];
      this.mainImage.push(file);
      let instance = M.Modal.getInstance($("#fileUploader"));
      instance.close();
    },
    initPlugins() {
      M.Chips.init(document.getElementById("pageTags"), {});
      tinymce.init({
        selector: "#id_cazary",
        plugins: ["link table code"],
        setup: (editor) => {
          editor.on("Change", (e) => {
            PageNewForm.content = tinymce.editors["id_cazary"].getContent();
            let content = this.getcontentText(PageNewForm.content, 200);
            content = content.replace(/(\r\n|\n|\r)/gm, "");
            this.setMetaContent(content, "description");
            this.setMetaContent(content, "og:description");
            this.setMetaContent(content, "twitter:description");
          });
        },
      });
      setTimeout(() => {
        M.Tabs.init(document.getElementById("formTabs"), {});
        var elems = document.getElementById("pageMetas");
        var instances = M.Collapsible.init(elems, {
          accordion: false,
        });
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
    setEditorContent: function (page) {
      console.log({page});
      let content = {
        id: 1,
        content: {
          raw: page.content,
          rendered: page.content,
        },
        date: "2020-11-24T21:22:54.913Z",
        date_gmt: "2020-11-24T21:22:54.913Z",
        title: { raw: "Preview page", rendered: "Preview page" },
        excerpt: { raw: "", rendered: "" },
        status: "draft",
        revisions: { count: 0, last_id: 0 },
        parent: 0,
        theme_style: true,
        type: "page",
        link: "http://localhost:8000/preview",
        categories: [],
        featured_media: 0,
        permalink_template: "http://localhost:8000/preview",
        preview_link: "http://localhost:8000/preview",
        _links: {
          "wp:action-assign-categories": [],
          "wp:action-create-categories": [],
        },
      };

      localStorage.setItem("g-editor-page", JSON.stringify(content));
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted PageNewForm") : null;
      this.initPlugins();
      this.checkEditMode();
      this.getCategories();
    });
  },
});
