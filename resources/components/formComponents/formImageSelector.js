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
