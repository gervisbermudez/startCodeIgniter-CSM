var PagesLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    pages: [],
    tableView: false,
    loader: true,
    filter: "",
    tempPage: {}
  },
  mixins: [mixins],
  computed: {
    filterPages: function () {
      return this.pages.filter((value, index) => {
        return !value.path.includes("blog/")
      });
    },
    blogs: function () {
        return this.pages.filter((value, index) => {
          return value.path.includes("blog/")
        });
    },
    filterAll: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.pages.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      }

      return this.pages;
    },
  },
  methods: {
    getcontentText: function (page) {
      var span = document.createElement("span");
      // Remover tags de imagen antes de parsear para evitar cargas de recursos
      var contentWithoutImages = page.content.replace(/<img[^>]*>/gi, '');
      span.innerHTML = contentWithoutImages;
      let text = span.textContent || span.innerText;
      return text.substring(0, 120) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(page) {
      if (page.imagen_file && page.imagen_file.file_front_path) {
        // Si file_front_path ya viene procesado desde el backend con /
        return page.imagen_file.file_front_path;
      }
      if (page.imagen_file) {
        return (
          BASEURL +
          page.imagen_file.file_path.substr(2) +
          page.imagen_file.file_name +
          "." +
          page.imagen_file.file_type
        );
      }
      return BASEURL + "public/img/default.jpg";
    },
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/",
        data: {},
        dataType: "json",
        success: function (response) {
          if (response && response.data) {
            self.pages = response.data;
            self.pages = self.pages.map((element) => {
              if (element.user) {
                element.user = new User(element.user);
              }
              return element;
            });
          }
          self.loader = false;
          self.$nextTick(() => {
            self.initPlugins();
          });
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    deletePage: function (page, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/pages/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.pages.splice(index, 1);
          }
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
    setTempPage: function (page, index) {
      this.tempPage.page = page;
      this.tempPage.index = index;
    },
    confirmDelete(data) {
      if (data) {
        this.deletePage(this.tempPage.page, this.tempPage.index);
      }
    },
    confirmArchive(data) {
      if (data) {
        this.toggleArchive(this.tempPage.page, this.tempPage.index);
      }
    },
    toggleArchive(page, index){
      var self = this;
      self.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/pages/archive/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.pages.splice(index, 1);
          }else{
            M.toast({ html: response.error_message });
          }
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
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
    });
  },
});
