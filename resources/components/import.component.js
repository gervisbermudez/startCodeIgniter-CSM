var Import = new Vue({
  el: "#root",
  data: {
    exportData: {
      pages: [],
      config: [],
    },
    tableView: true,
    loader: true,
    filter: "",
    selectedFile: false,
  },
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      return true;
    },
    selectedData: function () {
      return {
        pages: this.exportData.pages
          .filter((item) => item.checked)
          .map((item) => {
            return item.page_id;
          }),
        config: this.exportData.config
          .filter((item) => item.checked)
          .map((item) => {
            return item.site_config_id;
          }),
      };
    },
  },
  methods: {
    getcontentText: function (data) {
      return "";
    },
    handleFileSelect: function (evt) {
      var self = this;
      var files = evt.target.files; // FileList object

      // files is a FileList of File objects. List some properties.
      var output = [];
      for (var i = 0, f; (f = files[i]); i++) {
        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function (theFile) {
          return function (e) {
            try {
              self.selectedFile = true;
              fileContent = JSON.parse(e.target.result);
              console.log(fileContent);
              if (fileContent["pages"]) {
                self.exportData.pages = fileContent["pages"];
              }
              if (fileContent["config"]) {
                self.exportData.config = fileContent["config"];
              }
            } catch (ex) {
              console.error("ex when trying to parse json = " + ex);
              M.toast({ html: "Ocurrió un error inesperado" });
            }
          };
        })(f);
        reader.readAsText(f);
      }
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    saveData: function () {
      console.log("saveData fired");
      this.loader = true;

      var formData = new FormData();
      formData.append("exportData", JSON.stringify(this.selectedData));
      formData.append("import_file", $("#files").prop("files")[0]);

      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/import_file",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: (response) => {
          if (response.code == 200) {
            M.toast({ html: "file import was generate" });
          } else {
            M.toast({ html: "Ocurrió un error inesperado" });
          }
          this.loader = false;
        },
        error: (error) => {
          this.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    toggleData: function (items, itemsName) {
      items = items.map((item) => {
        return {
          ...item,
          checked: !item.checked,
        };
      });

      console.log(items);
      this.exportData[itemsName] = items;
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        var instances = M.Collapsible.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.loader = false;
      this.initPlugins();
    });
  },
});
