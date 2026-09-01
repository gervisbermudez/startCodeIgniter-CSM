Vue.component("formFieldTitle", {
  template: "#formFieldTitle-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "",
      fielApiID: "",
      title: null,
      custom_model_data_id: null,
      data: {},
    };
  },
  methods: {
    updateFielData: function (value) {
      this.data = this.data || {};
      this.data.fieldValue = value;
      this.title = value;
    },
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
    getData() {
      return {
        fieldPlaceholder: this.fieldPlaceholder,
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
      };
    },
    getContentData() {
      return {
        title: this.title,
      };
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.title = this.fieldData.custom_model_content_data_value.title;
      }
    });
  },
});

Vue.component("formFieldBoolean", {
  template: "#formFieldBoolean-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "Field Boolean",
      fielApiID: "field_boolean",
      title: null,
      custom_model_data_id: null,
      checkboxes: [
        {
          label: "Option",
          checked: true,
        },
      ],
    };
  },
  methods: {
    addOption(checkbox) {
      this.checkboxes.push({
        label: "Option",
        checked: true,
      });
    },
    setOption(checkbox) {},
    removeOption(index) {
      this.checkboxes.splice(index, 1);
    },
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
    getData() {
      return {
        fieldPlaceholder: this.fieldPlaceholder,
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
        checkboxes: JSON.stringify(this.checkboxes),
      };
    },
    getContentData() {
      return {
        bolean: this.checkboxes,
      };
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.checkboxes =
          this.fieldData.custom_model_content_data_value.bolean.map(
            (checkbox) => {
              return {
                label: checkbox.label,
                checked: checkbox.checked === "true",
              };
            }
          );
      }
    });
  },
});

Vue.component("formFieldNumber", {
  template: "#formFieldNumber-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "",
      fielApiID: "",
      number: null,
      custom_model_data_id: null,
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
    getData() {
      return {
        fieldPlaceholder: this.fieldPlaceholder,
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
      };
    },
    getContentData() {
      return {
        number: this.number,
      };
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".datepicker");
        var instances = M.Datepicker.init(elems, {
          format: "yyyy-mm-dd",
        });
      }, 1000);
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.title = this.fieldData.custom_model_content_data_value.title;
      }
      this.init();
    });
  },
});

Vue.component("formFieldDate", {
  template: "#formFieldDate-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "Field Date",
      fielApiID: "field_date",
      fielFormat: "yyyy-mm-dd",
      date: null,
      custom_model_data_id: null,
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
    getData() {
      return {
        fieldPlaceholder: this.fieldPlaceholder,
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
        fielFormat: this.fielFormat,
      };
    },
    getContentData() {
      return {
        date: this.date,
      };
    },
    setDate() {
      let date = document.getElementById(this.fieldID).value;
      this.date = date;
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".datepicker");
        var instance = M.Datepicker.init(elems, {
          format: this.fielFormat || "yyyy-mm-dd",
        });
        instance[0].setDate(new Date(this.date));
      }, 2000);
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.date = this.fieldData.custom_model_content_data_value.date;
      }
      this.init();
    });
  },
});

Vue.component("formFieldTime", {
  template: "#formFieldTime-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "Field Time",
      fielApiID: "field_time",
      time: null,
      custom_model_data_id: null,
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
    getData() {
      return {
        fieldPlaceholder: this.fieldPlaceholder,
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
      };
    },
    getContentData() {
      return {
        time: this.time,
      };
    },
    setTime() {
      let time = document.getElementById(this.fieldID).value;
      this.time = time;
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".timepicker");
        M.Timepicker.init(elems, {
          twelveHour: false,
        });
      }, 2000);
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.time = this.fieldData.custom_model_content_data_value.time;
      }
      this.init();
    });
  },
});

Vue.component("formFieldSelect", {
  template: "#formFieldselect-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      fieldPlaceholder: "",
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "Field select",
      fielApiID: "field_select",
      selectValue: null,
      selectOptions: [
        {
          label: "Option 1",
          value: 0,
        },
        {
          label: "Option 2",
          value: 1,
        },
        {
          label: "Option 3",
          value: 2,
        },
      ],
      custom_model_data_id: null,
    };
  },
  watch: {
    selectOptions: function (value) {
      this.updateSelect();
    },
  },
  methods: {
    convertfielApiID() {
      this.fielApiID = this.fieldName
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^\w-]+/g, "");
      this.fieldPlaceholder = this.fieldName.toLowerCase();
    },
    updateSelect() {
      var elems = document.getElementById(this.fieldID);
      if (elems) {
        if (elems.M_FormSelect) {
          elems.M_FormSelect.destroy();
        }
        setTimeout(() => {
          var instances = M.FormSelect.init(elems, {});
          console.log("select updated");
        }, 1000);
      }
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
    getData() {
      return {
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
        selectOptions: JSON.stringify(this.selectOptions),
      };
    },
    addOption() {
      this.selectOptions.push({
        label: "New Option",
        value: "0",
      });
    },
    setOption(option) {
      option.value = option.label;
      this.updateSelect();
    },
    removeOption(index) {
      this.selectOptions.splice(index, 1);
      this.updateSelect();
    },
    getContentData() {
      return {
        dropdown_select: this.selectValue,
      };
    },
    init() {
      setTimeout(() => {
        this.updateSelect();
        var elems = document.getElementById("collapsible" + this.fieldID);
        var instances = M.Collapsible.init(elems, {
          accordion: false,
        });
      }, 1000);
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.selectValue =
          this.fieldData.custom_model_content_data_value.dropdown_select;
      }
      this.init();
    });
  },
});

Vue.component("formFieldTextArea", {
  template: "#formFieldTextArea-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "",
      fielApiID: "",
      text: null,
      custom_model_data_id: null,
      fieldPlaceholder: "",
    };
  },
  methods: {
    convertfielApiID() {
      this.fielApiID = this.fieldName
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^\w-]+/g, "");
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
    getData() {
      return {
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
      };
    },
    getContentData() {
      return {
        text: this.text,
      };
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.text = this.fieldData.custom_model_content_data_value.text;
      }
    });
  },
});

Vue.component("formTextFormat", {
  template: "#formTextFormat-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      custom_model_fields_id: null,
      fieldID: "text_format_" + this.makeid(10),
      fieldName: "text_format",
      fielApiID: "text_format_" + this.makeid(4),
      text: null,
      custom_model_data_id: null,
      fieldPlaceholder: "",
    };
  },
  methods: {
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
    convertfielApiID() {
      this.fielApiID = this.fieldName
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^\w-]+/g, "");
    },
    getData() {
      return {
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
      };
    },
    getContentData() {
      return {
        formatText: this.text,
      };
    },
    init() {
      setTimeout(() => {
        tinymce.init({
          base_url: BASEURL + '/public/vendors/tinymce/js/tinymce',
          selector: "#" + this.fieldID,
          plugins: ["link table code"],
          setup: (editor) => {
            editor.on("Change", (e) => {
              this.text = tinymce.editors[this.fieldID].getContent();
            });
          },
        });
      }, 2000);
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.text = this.fieldData.custom_model_content_data_value.text;
      }
      this.init();
    });
  },
});

Vue.component("formImageSelector", {
  template: "#formImageSelector-template",
  props: [
    "tab-parent",
    "field-ref",
    "field-ref-index",
    "serveData",
    "configurable",
    "fieldData",
  ],
  data: function () {
    return {
      custom_model_fields_id: null,
      fieldID: this.makeid(10),
      fieldName: "Agregar Imagen",
      fielApiID: "file_imagen",
      text: null,
      custom_model_data_id: null,
      fieldPlaceholder: "",
      preselected: [],
      mode: "files",
      filter: "images",
      multiple: false,
    };
  },
  methods: {
    getPageImagePath(file) {
      if (file.get_full_file_path()) {
        return file.get_full_file_path();
      }
      return BASEURL + "/public/img/default.jpg";
    },
    removeItemImage(index) {
      this.preselected.splice(index, 1);
    },
    convertfielApiID() {
      this.fielApiID = this.fieldName
        .toLowerCase()
        .replace(/ /g, "_")
        .replace(/[^\w-]+/g, "");
    },
    copyCallcack(selectedFiles) {
      selectedFiles = selectedFiles.map((file) => new ExplorerFile(file));
      this.preselected = selectedFiles;
      let instance = M.Modal.getInstance($(".modal"));
      instance.close();
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
      }, 3000);
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
    getData() {
      return {
        fieldID: this.fieldID,
        fieldName: this.fieldName,
        fielApiID: this.fielApiID,
        custom_model_data_id: this.custom_model_data_id,
        mode: this.mode,
        filter: this.filter,
        multiple: this.multiple,
      };
    },
    getContentData() {
      return {
        image: this.preselected,
      };
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
      if (this.fieldData) {
        this.custom_model_fields_id = this.fieldData.custom_model_fields_id;
        this.custom_model_data_id = this.fieldData.custom_model_data_id;
        this.preselected =
          this.fieldData.custom_model_content_data_value.image.map(
            (file) => new ExplorerFile(file)
          );
      }

      if (this.multiple == "1") {
        this.multiple = true;
      } else {
        this.multiple = false;
      }
    });
  },
});

