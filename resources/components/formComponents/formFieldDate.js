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
