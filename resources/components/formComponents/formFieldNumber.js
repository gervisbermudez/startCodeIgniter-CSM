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
      form_field_id: null,
      fieldID: this.makeid(10),
      fieldName: "",
      fielApiID: "",
      number: null,
      form_custom_data_id: null,
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
        form_custom_data_id: this.form_custom_data_id,
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
        this.form_field_id = this.fieldData.form_field_id;
        this.form_custom_data_id = this.fieldData.form_custom_data_id;
        this.title = this.fieldData.form_value.title;
      }
      this.init();
    });
  },
});
