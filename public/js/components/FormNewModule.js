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
    custom_model_id: null,
    formsElements: formsElements,
    configurable: true,
  },
  methods: {
    getInitialTab() {
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

      this.tabs[this.getActiveTab()].custom_model_fields.push(
        JSON.parse(JSON.stringify(formField))
      );

      setTimeout(() => {
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        M.AutoInit();
      }, 2000);
    },
    removeField(tabindex, fieldindex) {
      if (
        this.tabs[tabindex].custom_model_fields[fieldindex].component ==
        "formTextFormat"
      ) {
        this.getfieldsData();
        editor = this.tabs[tabindex].custom_model_fields[fieldindex].data;
        tinymce.editors[editor.fieldID].destroy();
        this.tabs[tabindex].custom_model_fields.splice(fieldindex, 1);
      } else {
        this.tabs[tabindex].custom_model_fields.splice(fieldindex, 1);
      }
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
              JSON.parse(JSON.stringify(component.getData()))
            );
          }
        }
      }
    },
    setFieldData(tabID, fieldIndex, data) {
      this.debug ? console.log("setFieldData trigger") : null;
      this.tabs.map((element) => {
        if (element.tabID == tabID) {
          element.custom_model_fields[fieldIndex].data = data;
        }
      });
    },
    getFormData() {
      this.getfieldsData();
      return {
        custom_model_id: this.custom_model_id,
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
      this.loader = true;
      this.debug ? console.log("saveData trigger") : null;
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
          self.debug ? console.log(url, response) : null;
          if (response.data) {
            self.custom_model_id = response.data.custom_model_id;
            self.editMode = true;
            self.loader = false;
            M.toast({
              html: `<span>Formulario Guardado</span> <a class="btn-flat toast-action" href="${
                BASEURL + "admin/custommodels/addData/" + self.custom_model_id
              }"> Agregar Data</a>`,
            });
          }
        },
        error: function (response) {
          self.loader = false;
          M.toast({ html: response.error_message });
        },
      });
    },
    checkEditMode() {
      if (typeof custom_model_id != "undefined") {
        //cargar datos del formulario
        this.editMode = true;
        this.custom_model_id = custom_model_id;
        console.log("editMode", this.editMode);
        var self = this;
        var url = BASEURL + "api/v1/models/" + custom_model_id;
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
            M.toast({ html: "Ocurrió un error" });
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
          custom_model_tab_id: element.custom_model_tab_id,
          custom_model_fields: element.custom_model_fields
            ? element.custom_model_fields
            : [],
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
