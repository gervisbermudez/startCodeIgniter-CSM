var CustomModelModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    editMode: false,
    loader: true,
    form_name: "",
    form_description: "",
    slug: "",
    slugDirty: false,
    template: "default",
    title_field: "",
    templates: ["default", "portfolio", "cards", "team", "faq"],
    date_create: null,
    date_update: null,
    user: {},
    status: true,
    tabs: [],
    custom_model_id: null,
    formsElements: formsElements,
    configurable: true,
    i18n: window.COLLECTIONS_I18N || {},
    activePreset: "",
  },
  computed: {
    snippetText: function () {
      if (!this.slug) {
        return "";
      }
      return "{!! get_collection('" + this.slug + "') !!}";
    },
    apiFieldIds: function () {
      var ids = [];
      this.tabs.forEach(function (tab) {
        (tab.custom_model_fields || []).forEach(function (field) {
          var api =
            (field.data && field.data.fielApiID) || field.field_name || "";
          if (api && ids.indexOf(api) === -1) {
            ids.push(api);
          }
        });
      });
      return ids;
    },
  },
  mixins: [mixins],
  methods: {
    onNameInput: function () {
      if (!this.slugDirty) {
        this.slug = this.slugFromName(this.form_name);
      }
    },
    slugFromName: function (name) {
      return String(name || "")
        .toLowerCase()
        .trim()
        .replace(/[\s-]+/g, "_")
        .replace(/[^a-z0-9_]/g, "");
    },
    copySnippet: function () {
      var text = this.snippetText;
      if (!text) {
        return;
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text);
      } else {
        var input = document.getElementById("collection_snippet");
        if (input) {
          input.select();
          document.execCommand("copy");
        }
      }
      M.toast({ html: this.i18n.copied || "" });
    },
    applyPreset: function (key) {
      var map = {
        blank: {
          slug: "",
          template: "default",
          fields: [],
        },
        portfolio: {
          slug: "home_portfolio",
          template: "portfolio",
          fields: [
            { field_name: "title", displayName: "Title", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "image", displayName: "Image", icon: "image", component: "formImageSelector" },
            { field_name: "url", displayName: "URL", icon: "format_color_text", component: "formFieldTitle" },
          ],
        },
        team: {
          slug: "team",
          template: "team",
          fields: [
            { field_name: "title", displayName: "Name", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "role", displayName: "Role", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "image", displayName: "Image", icon: "image", component: "formImageSelector" },
          ],
        },
        faq: {
          slug: "faq",
          template: "faq",
          fields: [
            { field_name: "question", displayName: "Question", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "answer", displayName: "Answer", icon: "short_text", component: "formFieldTextArea" },
          ],
        },
        cards: {
          slug: "cards",
          template: "cards",
          fields: [
            { field_name: "title", displayName: "Title", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "text", displayName: "Text", icon: "short_text", component: "formFieldTextArea" },
            { field_name: "image", displayName: "Image", icon: "image", component: "formImageSelector" },
          ],
        },
        testimonials: {
          slug: "testimonials",
          template: "default",
          fields: [
            { field_name: "quote", displayName: "Quote", icon: "short_text", component: "formFieldTextArea" },
            { field_name: "title", displayName: "Author", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "role", displayName: "Role", icon: "format_color_text", component: "formFieldTitle" },
          ],
        },
        features: {
          slug: "features",
          template: "default",
          fields: [
            { field_name: "title", displayName: "Title", icon: "format_color_text", component: "formFieldTitle" },
            { field_name: "text", displayName: "Text", icon: "short_text", component: "formFieldTextArea" },
            { field_name: "icon", displayName: "Icon", icon: "format_color_text", component: "formFieldTitle" },
          ],
        },
      };
      var preset = map[key];
      if (!preset) {
        return;
      }
      this.activePreset = key;
      if (!this.slugDirty && preset.slug) {
        this.slug = preset.slug;
      }
      this.template = preset.template || "default";
      var names = (this.i18n && this.i18n.presetNames) || {};
      if (!this.form_name && names[key]) {
        this.form_name = names[key];
        if (!this.slugDirty) {
          this.slug = preset.slug || this.slugFromName(this.form_name);
        }
      }
      var tab = this.tabs[this.getActiveTab()] || this.tabs[0];
      if (!tab) {
        this.addTab();
        tab = this.tabs[0];
      }
      var fields = [];
      preset.fields.forEach(function (def) {
        var base = null;
        formsElements.forEach(function (el) {
          if (el.component === def.component) {
            base = JSON.parse(JSON.stringify(el));
          }
        });
        if (!base) {
          base = { data: {}, status: "1" };
        }
        base.field_name = def.field_name;
        base.displayName = def.displayName;
        base.icon = def.icon;
        base.component = def.component;
        base.data = base.data || {};
        base.data.fielApiID = def.field_name;
        base.data.fieldName = def.field_name;
        fields.push(base);
      });
      this.$set(tab, "custom_model_fields", fields);
      if (!this.title_field && fields.length) {
        this.title_field = fields[0].field_name;
      }
      var self = this;
      this.$nextTick(function () {
        self.initFieldPlugins();
      });
      if (this.i18n && this.i18n.presetApplied) {
        M.toast({ html: this.i18n.presetApplied });
      }
    },
    getInitialTab: function () {
      return {
        tab_name: "Tab " + (this.tabs.length + 1),
        tabID: this.makeid(10),
        custom_model_tab_id: null,
        custom_model_fields: [],
        edited: true,
        active: true,
        status: true,
      };
    },
    setActive: function (index) {
      this.tabs.map(function (el) {
        el.active = false;
        return el;
      });
      this.tabs[index].active = true;
    },
    addTab: function () {
      this.tabs.push(this.getInitialTab());
      this.setActive(this.tabs.length - 1);
    },
    saveTab: function (tab) {
      this.tabs[tab].edited = false;
    },
    makeid: function (length) {
      var result = "";
      var characters =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
      var charactersLength = characters.length;
      for (var i = 0; i < length; i++) {
        result += characters.charAt(
          Math.floor(Math.random() * charactersLength)
        );
      }
      return result;
    },
    deleteTab: function (index) {
      if (this.tabs.length == 1) {
        return false;
      }
      this.tabs.splice(index, 1);
    },
    getActiveTab: function () {
      var activeTab = 0;
      this.tabs.forEach(function (element, index) {
        if (element.active) {
          activeTab = index;
        }
      });
      return activeTab;
    },
    addField: function (formField) {
      this.tabs[this.getActiveTab()].custom_model_fields.push(
        JSON.parse(JSON.stringify(formField))
      );
      var self = this;
      this.$nextTick(function () {
        self.initFieldPlugins();
      });
    },
    initFieldPlugins: function () {
      var elems = document.querySelectorAll(".collapsible:not(#slide-out)");
      M.Collapsible.init(elems, {});
      elems = document.querySelectorAll(".tooltipped");
      M.Tooltip.init(elems, {});
      elems = document.querySelectorAll("select");
      M.FormSelect.init(elems, {});
    },
    removeField: function (tabindex, fieldindex) {
      if (
        this.tabs[tabindex].custom_model_fields[fieldindex].component ==
        "formTextFormat"
      ) {
        this.getfieldsData();
        var editor = this.tabs[tabindex].custom_model_fields[fieldindex].data;
        if (typeof tinymce !== "undefined" && tinymce.editors[editor.fieldID]) {
          tinymce.editors[editor.fieldID].destroy();
        }
        this.tabs[tabindex].custom_model_fields.splice(fieldindex, 1);
      } else {
        this.tabs[tabindex].custom_model_fields.splice(fieldindex, 1);
      }
    },
    getfieldsData: function () {
      var fieldsComponents = CustomModelModule.$refs;
      for (var key in fieldsComponents) {
        if (fieldsComponents.hasOwnProperty(key)) {
          var element = fieldsComponents[key];
          for (var index = 0; index < element.length; index++) {
            var component = element[index];
            CustomModelModule.setFieldData(
              component.tabParent.tabID,
              component.fieldRefIndex,
              JSON.parse(JSON.stringify(component.getData()))
            );
          }
        }
      }
    },
    setFieldData: function (tabID, fieldIndex, data) {
      this.tabs.map(function (element) {
        if (element.tabID == tabID) {
          element.custom_model_fields[fieldIndex].data = data;
        }
      });
    },
    getFormData: function () {
      this.getfieldsData();
      return {
        custom_model_id: this.custom_model_id,
        form_name: this.form_name,
        form_description: this.form_description,
        slug: this.slug,
        template: this.template,
        title_field: this.title_field,
        date_create: this.date_create,
        date_update: this.date_update,
        status: this.status ? 1 : 3,
        user: this.user,
        tabs: this.tabs,
      };
    },
    validateClient: function () {
      if (!this.form_name) {
        M.toast({ html: this.i18n.needField || "" });
        return false;
      }
      if (!this.slug || !/^[a-z0-9_]+$/.test(this.slug)) {
        M.toast({ html: this.i18n.slugInvalid || "" });
        return false;
      }
      var fields = 0;
      this.tabs.forEach(function (tab) {
        fields += (tab.custom_model_fields || []).length;
      });
      if (!this.tabs.length || fields < 1) {
        M.toast({ html: this.i18n.needField || "" });
        return false;
      }
      return true;
    },
    saveData: function () {
      if (!this.validateClient()) {
        return;
      }
      $("html, body").animate({ scrollTop: 0 }, 600);
      this.loader = true;
      var data = this.getFormData();
      var url = BASEURL + "api/v1/models";
      var self = this;
      $.ajax({
        type: "POST",
        url: url,
        data: {
          data: JSON.stringify(data),
        },
        dataType: "json",
        success: function (response) {
          if (response.data) {
            self.custom_model_id = response.data.custom_model_id;
            self.editMode = true;
            self.loader = false;
            var addHref =
              BASEURL + "admin/custommodels/addData/" + self.custom_model_id;
            var itemsHref =
              BASEURL + "admin/custommodels/items/" + self.custom_model_id;
            M.toast({
              html:
                "<span>" +
                (self.i18n.saved || "") +
                '</span> <a class="btn-flat toast-action" href="' +
                addHref +
                '">' +
                (self.i18n.addItem || "") +
                '</a> <a class="btn-flat toast-action" href="' +
                itemsHref +
                '">' +
                (self.i18n.viewItems || "") +
                "</a>",
            });
          } else {
            self.loader = false;
            M.toast({ html: self.i18n.error || "" });
          }
        },
        error: function (xhr) {
          self.loader = false;
          var msg = self.i18n.error || "";
          if (xhr.responseJSON && xhr.responseJSON.error_message) {
            msg = xhr.responseJSON.error_message;
          }
          M.toast({ html: msg });
        },
      });
    },
    loadTemplates: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/models/templates",
        dataType: "json",
        success: function (response) {
          if (response.data && response.data.length) {
            self.templates = response.data;
          }
          self.$nextTick(function () {
            var elems = document.querySelectorAll("select");
            M.FormSelect.init(elems, {});
          });
        },
      });
    },
    checkEditMode: function () {
      if (typeof custom_model_id != "undefined") {
        this.editMode = true;
        this.custom_model_id = custom_model_id;
        var self = this;
        $.ajax({
          type: "GET",
          url: BASEURL + "api/v1/models/" + custom_model_id,
          dataType: "json",
          success: function (response) {
            if (response.code == "200") {
              self.updateFormData(response.data);
            }
          },
          error: function () {
            self.loader = false;
            M.toast({ html: self.i18n.error || "" });
          },
        });
      } else {
        this.loader = false;
        this.$nextTick(function () {
          var elems = document.querySelectorAll("select");
          M.FormSelect.init(elems, {});
        });
      }
    },
    updateFormData: function (data) {
      var self = this;
      this.serverdata = data;
      this.form_name = data.form_name;
      this.form_description = data.form_description;
      this.slug = data.slug || this.slugFromName(data.form_name);
      this.slugDirty = true;
      this.template = data.template || "default";
      this.title_field = data.title_field || "";
      this.date_create = data.date_create;
      this.date_update = data.date_update;
      this.status = data.status == "1" || data.status == 1;
      this.user = data.user;
      this.tabs = (data.tabs || []).map(function (element) {
        return {
          edited: false,
          active: false,
          tab_name: element.tab_name,
          status: element.status == "1",
          custom_model_tab_id: element.custom_model_tab_id,
          custom_model_fields: element.custom_model_fields
            ? element.custom_model_fields
            : [],
          tabID: self.makeid(10),
        };
      });
      if (this.tabs.length) {
        this.tabs[0].active = true;
      }
      this.$nextTick(function () {
        self.initFieldPlugins();
        self.loader = false;
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.tabs.push(this.getInitialTab());
      this.tabs[0].edited = false;
      this.loadTemplates();
      this.checkEditMode();
    });
  },
});
