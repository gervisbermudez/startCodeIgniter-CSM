var searchComponent = new Vue({
  el: "#root",
  data: {
    items: [],
    tableView: true,
    loader: true,
    filter: "",
    toDeleteItem: {},
    searchTerm: null,
    searchResults: false,
    categoriesColumns: [
      {
        colum: "name",
        label: "name",
      },
      {
        colum: "type",
        label: "Type",
      },
      {
        colum: "description",
        label: "description",
        format: (item, colum) => {
          var span = document.createElement("span");
          span.innerHTML = item.description;
          let text = span.textContent || span.innerText;
          return text.substring(0, 50) + "...";
        },
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "user_id",
        label: "Options",
        format: (item, colum) => {
          return `<a href="${
            BASEURL + "admin/categorias/editar/" + item.categorie_id
          }">Edit</a>`;
        },
      },
    ],
    form_customsColumns: [
      {
        colum: "form_name",
        label: "name",
      },
      {
        colum: "model_type",
        label: "Type",
      },
      {
        colum: "form_description",
        label: "Description",
        format: (item, colum) => {
          var span = document.createElement("span");
          span.innerHTML = item.form_description;
          let text = span.textContent || span.innerText;
          return text.substring(0, 50) + "...";
        },
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "user_id",
        label: "Options",
        format: (item, colum) => {
          return `<a href="${
            BASEURL + "admin/formularios/editForm/" + item.form_custom_id
          }">Edit</a>`;
        },
      },
    ],
    form_contentsColumns: [
      {
        colum: "model_type",
        label: "type",
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "user_id",
        label: "Options",
        format: (item, colum) => {
          return `<a href="${
            BASEURL +
            "admin/formularios/editData/" +
            item.form_custom_id +
            "/" +
            item.form_content_id
          }">Edit</a>`;
        },
      },
    ],
    siteformsColumns: [
      {
        colum: "name",
        label: "Name",
      },
      {
        colum: "template",
        label: "Template",
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "user_id",
        label: "Options",
        format: (item, colum) => {
          return `<a href="${
            BASEURL + "admin/siteforms/editar/" + item.siteform_id
          }">Edit</a>`;
        },
      },
    ],
    menusColumns: [
      {
        colum: "name",
        label: "Name",
      },
      {
        colum: "template",
        label: "Template",
      },
      {
        colum: "position",
        label: "Position",
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "user_id",
        label: "Options",
        format: (item, colum) => {
          return `<a href="${
            BASEURL + "admin/siteforms/editar/" + item.menu_id
          }">Edit</a>`;
        },
      },
    ],
  },
  mixins: [mixins],
  computed: {
    filterMenus: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.items.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.items;
      }
    },
    websiteCountResults: function () {
      if (this.searchResults === false) {
        return 0;
      }
      return (
        this.searchResults.pages.length +
        this.searchResults.form_customs.length +
        this.searchResults.form_contents.length +
        this.searchResults.siteforms.length +
        this.searchResults.menus.length +
        this.searchResults.categories.length +
        this.searchResults.albumes.length
      );
    },
  },
  methods: {
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(item) {
      if (item.imagen_file) {
        return BASEURL + item.imagen_file.file_front_path;
      }
      return BASEURL + "public/img/default.jpg";
    },
    getFullFilePath(file) {
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    getIcon(file) {
      let icon = "far fa-file";
      switch (file.file_type) {
        case "folder":
          icon = "far fa-folder";
          break;
        case "jpg":
        case "jpeg":
        case "png":
        case "gif":
          icon = "fas fa-file-image";
          break;
        case "html":
          icon = "fab fa-html5";
          break;
        case "scss":
          icon = "fab fa-sass";
          break;
        case "css":
        case "min.css":
          icon = "fab fa-css3-alt";
          break;
        case "txt":
          icon = "far fa-file-alt";
          break;
        case "php":
        case "blade.php":
          icon = "fab fa-php";
          break;
        case "js":
        case "json":
        case "min.js":
          icon = "fab fa-js";
          break;
        case "eot":
        case "otf":
        case "woff2":
          icon = "fas fa-font";
          break;
      }
      return icon;
    },
    getExtention(file) {
      if (file.file_type == "folder") {
        return "";
      } else {
        return "." + file.file_type;
      }
    },
    isImage(file) {
      if (
        file.file_type == "jpg" ||
        file.file_type == "jpeg" ||
        file.file_type == "png" ||
        file.file_type == "gif"
      ) {
        return true;
      }
      return false;
    },
    getImagePath(file) {
      if (this.isImage(file)) {
        return (
          BASEURL +
          file.file_path.substr(2) +
          file.file_name +
          "." +
          file.file_type
        );
      }
    },
    getAlbumImagePath(album) {
      if (album.items.length) {
        return BASEURL + album.items[0].file.file_front_path;
      }
      return BASEURL + "public/img/default.jpg";
    },
    performSearch: function () {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/search/?q=" + this.searchTerm,
        data: {},
        dataType: "json",
        success: function (response) {
          self.searchResults = response.data;
          self.searchResults.users = response.data.users.map((element) => {
            return new User(element);
          });
          self.loader = false;
          self.initPlugins();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".tabs");
        M.Tabs.init(elems, {});
      }, 100);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      const urlParams = new URLSearchParams(window.location.search);
      const q = urlParams.get("q");
      if (q) {
        this.searchTerm = q;
        this.performSearch();
      } else {
        this.loader = false;
      }
    });
  },
});
