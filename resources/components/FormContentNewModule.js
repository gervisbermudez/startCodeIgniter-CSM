var FormNewModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    editMode: false,
    loader: true,
    form_name: "Nuevo Formulario",
    form_description: "",
    date_create: null,
    date_update: null,
    user: {},
    status: true,
    tabs: [],
    form_custom_id: null,
    form_content_id: null,
    formsElements: [
      {
        field_name: "title",
        displayName: "Titulo",
        icon: "format_color_text",
        component: "formFieldTitle",
        status: "1",
        data: {},
      },
      {
        field_name: "formatText",
        displayName: "Texto con formato",
        icon: "format_size",
        status: "1",
        data: {},
      },
      {
        field_name: "image",
        displayName: "Imagen",
        icon: "image",
        status: "1",
        data: {},
      },
      {
        field_name: "link",
        displayName: "Link",
        icon: "insert_link",
        status: "1",
        data: {},
      },
      {
        field_name: "date",
        displayName: "Fecha",
        icon: "date_range",
        status: "1",
        data: {},
      },
      {
        field_name: "timestamp",
        displayName: "Fecha y Hora",
        icon: "access_time",
        status: "1",
        data: {},
      },
      {
        field_name: "number",
        displayName: "Numero",
        icon: "looks_one",
        status: "1",
        data: {},
      },
      {
        field_name: "dropdown_select",
        displayName: "Select",
        status: "1",
        icon: "list",
        data: {},
      },
      {
        field_name: "bolean",
        displayName: "Bolean",
        status: "1",
        icon: "check_circle",
        data: {},
      },
    ],
    configurable: false,
  },
  methods: {
    getInitialTab() {
      return {
        tab_name: "Tab " + (this.tabs.length + 1),
        tabID: this.makeid(10),
        form_tab_id: null,
        form_fields: [],
        edited: true,
        active: true,
        status: true,
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

      this.tabs[this.getActiveTab()].form_fields.push(
        JSON.parse(JSON.stringify(formField))
      );

      setTimeout(() => {
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
      }, 2000);
    },
    getfieldsData() {
      this.debug ? console.log("getfieldsData trigger") : null;
      fieldsComponents = FormNewModule.$refs;
      for (const key in fieldsComponents) {
        if (fieldsComponents.hasOwnProperty(key)) {
          const element = fieldsComponents[key];
          for (let index = 0; index < element.length; index++) {
            const component = element[index];
            FormNewModule.setFieldData(
              component.tabParent.tabID,
              component.fieldRefIndex,
              JSON.parse(JSON.stringify(component.getContentData()))
            );
          }
        }
      }
    },
    setFieldData(tabID, fieldIndex, data) {
      this.debug ? console.log("setFieldData trigger") : null;
      this.tabs.map((element) => {
        if (element.tabID == tabID) {
          element.form_fields[fieldIndex].data = data;
        }
      });
    },
    getFormData() {
      this.getfieldsData();
      return {
        form_custom_id: this.form_custom_id,
        form_content_id: this.form_content_id,
        form_name: this.form_name,
        form_description: this.form_description,
        date_create: this.date_create,
        date_update: this.date_update,
        status: this.status ? 1 : 0,
        user: this.user,
        tabs: this.tabs,
      };
    },
    saveData() {
      $("html, body").animate({ scrollTop: 0 }, 600);
      this.debug ? console.log("saveData trigger") : null;
      var data = this.getFormData();
      var url = BASEURL + "api/v1/forms/data";
      console.log(data);
      this.loader = true;
      var self = this;
      $.ajax({
        type: "POST",
        url: url,
        data: {
          data: data,
        },
        dataType: "json",
        success: function (response) {
          self.debug ? console.log(url, response) : null;
          if (response.data) {
            self.form_custom_id = response.data.form_custom_id;
            self.editMode = true;
            self.loader = false;
            M.toast({ html: 'Datos Guardados' });
          }
        },
        error: function (response) {
          self.loader = false;
          console.log(response);
          M.toast({ html: 'Ocurrió un error' });

        },
      });
    },
    checkEditMode() {
      if (typeof form_custom_id != "undefined") {
        //cargar datos del formulario
        this.editMode = true;
        this.form_custom_id = form_custom_id;
        console.log("editMode", this.editMode);
        var self = this;
        var url = BASEURL + "api/v1/forms/" + form_custom_id;
        $.ajax({
          type: "GET",
          url: url,
          data: {},
          dataType: "json",
          success: function (response) {
            self.debug ? console.log(url, response) : null;
            if (response.code == "200") {
              self.updateFormData(response.data);
            }
          },
          error: function (response) {
            self.loader = false;
            M.toast({ html: 'Ocurrió un error' });
          },
        });
      } else {
        this.loader = false;
      }
    },
    updateFormData(data) {
      this.serverdata = data;
      this.form_name = data.form_name;
      this.form_description = data.form_description;
      this.date_create = data.date_create;
      this.date_update = data.date_update;
      this.status = data.status == "1";
      this.user = data.user;
      this.tabs = data.tabs.map((element, index) => {
        return {
          edited: false,
          active: false,
          tab_name: element.tab_name,
          status: element.status == "1",
          form_tab_id: element.form_tab_id,
          form_fields: element.form_fields ? element.form_fields : [],
          tabID: this.makeid(10),
        };
      });

      this.tabs[0].active = true;
      setTimeout(() => {
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
        this.loader = false;
      }, 2000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("FormNewModule mounted") : null;
      this.tabs.push(this.getInitialTab());
      this.tabs[0].edited = false;
      this.checkEditMode();
    });
  },
});
