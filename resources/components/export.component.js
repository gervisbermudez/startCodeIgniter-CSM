var Export = new Vue({
  el: "#root",
  data: {
    exportData: {
      pages: [],
      config: [],
    },
    tableView: true,
    loader: true,
    filter: "",
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
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getData: function () {
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/config/export_data",
        data: {},
        dataType: "json",
        success: (response) => {
          this.exportData = response.data;
          this.exportData["pages"] = response.data.pages.map((page) => {
            return {
              ...page,
              checked: false,
              user: new User(page.user),
            };
          });
          this.exportData["config"] = response.data.config.map((item) => {
            return {
              ...item,
              checked: false,
              user: new User(item.user),
              config_data: JSON.parse(item.config_data),
            };
          });
          setTimeout(() => {
            this.loader = false;
            this.initPlugins();
          }, 1000);
        },
        error: (error) => {
          this.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    generateFile: function () {
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/config/generate_export_file",
        data: {
          exportData: this.selectedData,
        },
        dataType: "json",
        success: (response) => {
          if (response.code == 200) {
            M.toast({ html: "file export was generate" });
            this.download_export_file(response.data.exportFilename);
          } else {
            M.toast({ html: "Ocurrió un error inesperado" });
          }
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
    download_export_file: function (fileName) {
      this.loader = true;
      fetch(BASEURL + "temp/" + fileName)
        .then((resp) => resp.blob())
        .then((blob) => {
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.style.display = "none";
          a.href = url;
          a.download = fileName;
          document.body.appendChild(a);
          a.click();
          window.URL.revokeObjectURL(url);
          M.toast({ html: "Your file is starting to loading" });
          this.loader = false;
        })
        .catch((error) => {
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
          this.loader = false;
        });
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
      this.getData();
    });
  },
});
