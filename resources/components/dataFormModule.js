Vue.component("formFieldTitle", {
  template: "#formFieldTitle-template",
  props: ["tab-parent", "field-ref", "field-ref-index", "serve-data"],
  data: function () {
    return {
      debug: DEBUGMODE,
      fieldPlaceholder: "",
      fieldID: this.makeid(10),
      fieldName: "",
      fielApiID: "",
      data: {},
    };
  },
  methods: {
    convertfielApiID() {
      this.fielApiID = this.fieldName
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^\w-]+/g, "");
      this.fieldPlaceholder = this.fieldName.toLowerCase();
    },
    makeid(length) {
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
    updateFielData(value) {
      this.data.fieldValue = value;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      for (const key in this.serveData) {
        if (this.serveData.hasOwnProperty(key)) {
          const element = this.serveData[key];
          this[key] = element;
        }
      }
    });
  },
});

var dataFormModule = new Vue({
  el: "#dataFormModule",
  data: {
    debug: DEBUGMODE,
    editMode: false,
    loader: true,
    tabs: [],
    custom_model_id: null,
    formsElements: [
      {
        name: "title",
        displayName: "Titulo",
        icon: "format_color_text",
        component: "formFieldTitle",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "formatText",
        displayName: "Texto con formato",
        icon: "format_size",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "image",
        displayName: "Imagen",
        icon: "image",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "link",
        displayName: "Link",
        icon: "insert_link",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "date",
        displayName: "Fecha",
        icon: "date_range",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "timestamp",
        displayName: "Fecha y Hora",
        icon: "access_time",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "number",
        displayName: "Numero",
        icon: "looks_one",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "dropdown_select",
        displayName: "Select",
        icon: "list",
        data: {
          hola: "mundo",
        },
      },
      {
        name: "bolean",
        displayName: "Bolean",
        icon: "check_circle",
        data: {
          hola: "mundo",
        },
      },
    ],
    form_name: "",
    form_status: true,
    fieldsData: [],
  },
  computed: {
    getFormName() {
      return this.editMode ? "Edit " + this.form_name : "New " + this.form_name;
    },
  },
  methods: {
    getInitialTab() {
      return {
        name: "Tab " + (this.tabs.length + 1),
        tabID: this.makeid(10),
        edited: true,
        active: true,
        fields: [],
      };
    },
    setActive(index) {
      this.tabs.map((el) => {
        el.active = false;
        return el;
      });
      this.tabs[index].active = true;
    },
    addTab() {
      this.debug ? console.log("addTab trigger") : null;
      this.tabs.push(this.getInitialTab());
      this.setActive(this.tabs.length - 1);
    },
    saveTab(tab) {
      this.debug ? console.log("saveTab trigger") : null;
      this.tabs[tab].edited = false;
    },
    makeid(length) {
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
    deleteTab(index) {
      this.debug ? console.log("deleteTab trigger") : null;
      if (this.tabs.length == 1) {
        return false;
      }
      this.tabs.splice(index, 1);
    },
    getActiveTab() {
      let activeTab;
      this.tabs.forEach((element, index) => {
        if (element.active) {
          activeTab = index;
        }
      });
      return activeTab;
    },
    addField(formField) {
      this.debug ? console.log("addField trigger") : null;

      this.tabs[this.getActiveTab()].fields.push(
        JSON.parse(JSON.stringify(formField))
      );

      setTimeout(() => {
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
      }, 2000);
    },
    getfieldsData() {
      this.debug ? console.log("getfieldsData trigger") : null;
      fieldsComponents = dataFormModule.$refs;
      for (const key in fieldsComponents) {
        if (fieldsComponents.hasOwnProperty(key)) {
          const element = fieldsComponents[key];
          for (let index = 0; index < element.length; index++) {
            const component = element[index];
            dataFormModule.setFieldData(
              component.tabParent.tabID,
              component.fieldRefIndex,
              JSON.parse(JSON.stringify(component.$data))
            );
          }
        }
      }
    },
    setFieldData(tabID, fieldIndex, data) {
      this.debug ? console.log("setFieldData trigger") : null;
      this.tabs.map((element) => {
        if (element.tabID == tabID) {
          element.fields[fieldIndex].data = data;
        }
      });
    },
    saveData() {
      this.loader = true;
      $("html, body").animate({ scrollTop: 0 }, 600);
      this.debug ? console.log("saveData trigger") : null;
      this.getfieldsData();
      let data = {
        form_name: this.form_name,
        form_status: this.form_status ? 1 : 0,
        tabs: {},
        custom_model_id: this.custom_model_id,
      };
      if (this.editMode && this.custom_model_content_id) {
        data.custom_model_content_id = custom_model_content_id;
      }
      this.tabs.forEach((element) => {
        if (element.fields.length < 0) {
          return false;
        }
        data.tabs[element.name] = {
          tab_name: element.name,
          fields: element.fields,
        };
      });
      var url = BASEURL + "api/v1/models/data/";
      $.ajax({
        type: "POST",
        url: url,
        data: {
          data: data,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            this.custom_model_content_id = response.data;
            M.toast({ html: "Datos guardados!" });
            setTimeout(() => {
              window.location = BASEURL + "admin/CustomModels/content";
            }, 3000);
          } else {
            M.toast({ html: "Ocurrio un error" });
          }
        },
        error: function (param) {
          M.toast({ html: "Ocurrio un error" });
        },
      });
    },
    loadFormFields() {
      if (typeof custom_model_id != "undefined") {
        //cargar campos del formulario
        this.custom_model_id = custom_model_id;
        var self = this;
        var url = BASEURL + "api/v1/models/" + custom_model_id;
        $.ajax({
          type: "GET",
          url: url,
          data: {},
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.updateFields(response.data);
            } else {
              M.toast({ html: "Ocurrio un error" });
            }
          },
          error: function (param) {
            M.toast({ html: "Ocurrio un error" });
          },
        });
      } else {
        this.loader = true;
        M.toast({ html: "Ocurrio un error" });
      }
    },
    checkEditMode() {
      if (custom_model_content_id && typeof editMode != "undefined") {
        this.custom_model_content_id = custom_model_content_id;
        this.editMode = editMode;
        this.debug ? console.log("editMode") : null;

        var self = this;
        var url = BASEURL + "api/v1/models/data/" + custom_model_content_id;
        $.ajax({
          type: "GET",
          url: url,
          data: {},
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            if (response.code == 200) {
              self.updateFieldsData(response.data);
            } else {
              M.toast({ html: "Ocurrio un error" });
            }
          },
          error: function (param) {
            M.toast({ html: "Ocurrio un error" });
          },
        });
      } else {
        this.loader = false;
      }
    },
    updateFields(data) {
      this.form_name = data[0].form_name;
      this.form_status = data[0].status == "1";
      this.loader = false;
      this.tabs = [];
      data.forEach((element) => {
        let tab = this.getInitialTab();
        tab.edited = false;
        tab.active = false;
        tab.name = element.tab_name;
        let tempField = {};
        element.fields_data.forEach((field) => {
          tempField = {
            component: field.component,
            displayName: field.displayName,
            icon: field.icon,
            name: field.field_name,
            serveData: field.dataconfigs,
          };
          tab.fields.push(tempField);
        });

        this.tabs.push(tab);
      });
      this.tabs[0].active = true;
    },
    updateFieldsData(data) {
      var self = this;
      self.fieldsData = data;
      values = JSON.parse(self.fieldsData[0].form_data);
      values.forEach((element) => {
        this.debug ? console.log(element) : null;
        for (const key in element) {
          if (element.hasOwnProperty(key)) {
            const value = element[key];
            self.setFieldDataFromServe(key, value);
          }
        }
      });
    },
    setFieldDataFromServe(fieldName, fieldValue) {
      fieldsComponents = dataFormModule.$refs;
      for (const key in fieldsComponents) {
        if (fieldsComponents.hasOwnProperty(key)) {
          const element = fieldsComponents[key];
          for (let index = 0; index < element.length; index++) {
            const component = element[index];
            this.debug ? console.log(component) : null;
            if (component.fielApiID == fieldName) {
              component.updateFielData(fieldValue);
            }
            component.$forceUpdate();
          }
        }
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("dataFormModule mounted") : null;
      this.tabs.push(this.getInitialTab());
      this.tabs[0].edited = false;
      this.loadFormFields();
      this.checkEditMode();
    });
  },
});
