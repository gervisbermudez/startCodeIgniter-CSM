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
