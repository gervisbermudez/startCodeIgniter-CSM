var PagesLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    pages: [],
    tableView: false,
    loader: true,
    filter: "",
    tempPage: {},
    currentStatus: null, // null means Published + Drafts
    serverPagination: true,
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
      if (this.serverPagination) {
        return this.pages;
      }
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
      return this.truncate(text, 120);
    },
    truncate: function (text, length) {
      if (!text) return "";
      if (text.length <= length) return text;
      return text.substring(0, length) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    reloadList: function (page) {
      this.getPages(this.currentStatus, page);
    },
    getPages: function (status, page) {
      var self = this;
      if (typeof status === "undefined") {
        status = this.currentStatus;
      }
      if (status !== this.currentStatus) {
        page = 1;
      }
      if (typeof page === "undefined") {
        page = this.currentPage || 1;
      }
      self.loader = true;
      self.currentStatus = status;
      var extra = {};
      if (status !== null) {
        extra.status = status;
      }
      var data = this.listQuery(extra, page);

      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/",
        data: data,
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
          } else {
            self.pages = [];
          }
          self.applyPaginatorFromResponse(response);
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
      return BASEURL + "/public/img/default.jpg";
    },
    deletePage: function (page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/pages/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Page deleted successfully" });
            self.getPages(self.currentStatus);
            return;
          }
          self.loader = false;
          self.initPlugins();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
          console.error(error);
        },
      });
    },
    duplicatePage: function (page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/pages/duplicate/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Page duplicated successfully" });
            setTimeout(() => {
              window.location.href = BASEURL + "admin/pages/editar/" + response.data.page_id;
            }, 1000);
          } else {
            M.toast({ html: "Could not duplicate page" });
          }
          self.loader = false;
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
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
        this.deletePage(this.tempPage.page);
      }
    },
    confirmArchive(data) {
      if (data) {
        this.toggleArchive(this.tempPage.page);
      }
    },
    toggleArchive(page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/pages/archive/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Page archived successfully" });
            self.getPages(self.currentStatus);
            return;
          } else {
            M.toast({ html: response.error_message });
          }
          self.loader = false;
          self.initPlugins();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
          console.error(error);
        },
      });
    },
    restorePage(page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/pages/restore/" + page.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Page restored as draft" });
            self.getPages(self.currentStatus);
            return;
          } else {
            M.toast({ html: response.error_message });
          }
          self.loader = false;
          self.initPlugins();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
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
        var modalElems = document.querySelectorAll(".modal");
        M.Modal.init(modalElems, {});
      }, 1000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
    });
  },
});
