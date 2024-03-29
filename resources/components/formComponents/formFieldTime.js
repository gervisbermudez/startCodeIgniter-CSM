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
