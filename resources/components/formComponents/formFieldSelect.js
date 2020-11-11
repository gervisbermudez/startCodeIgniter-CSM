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
      form_field_id: null,
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
      form_custom_data_id: null,
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
        form_custom_data_id: this.form_custom_data_id,
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
        this.form_field_id = this.fieldData.form_field_id;
        this.form_custom_data_id = this.fieldData.form_custom_data_id;
        this.selectValue = this.fieldData.form_value.dropdown_select;
      }
      this.init();
    });
  },
});
