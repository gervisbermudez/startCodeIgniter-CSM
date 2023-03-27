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
