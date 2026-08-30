var PagesLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    pages: [],
    tableView: false,
    loader: true,
    filter: "",
    tempPage: {},
    currentStatus: null,
    serverPagination: true,
    listEndpoint: "api/v1/pages",
    listKey: "pages",
    listPk: "page_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterPages: function () {
      return this.filterAll.filter(function (value) {
        return !value.path || value.path.indexOf("blog/") === -1;
      });
    },
    blogs: function () {
      return this.filterAll.filter(function (value) {
        return value.path && value.path.indexOf("blog/") !== -1;
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
    listExtraQuery: function () {
      if (this.currentStatus !== null) {
        return { status: this.currentStatus };
      }
      return {};
    },
    reloadList: function (page) {
      this.getPages(this.currentStatus, page);
    },
    getPages: function (status, page) {
      if (typeof status === "undefined") {
        status = this.currentStatus;
      }
      if (status !== this.currentStatus) {
        page = 1;
      }
      this.currentStatus = status;
      this.fetchList(page);
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
            self.toast("toast_deleted");
            self.getPages(self.currentStatus);
            return;
          }
          self.loader = false;
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
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
            self.toast("toast_duplicated");
            setTimeout(function () {
              window.location.href = BASEURL + "admin/pages/editar/" + response.data.page_id;
            }, 1000);
          } else {
            self.toastError(null, response);
          }
          self.loader = false;
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
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
            self.toast("toast_archived");
            self.getPages(self.currentStatus);
            return;
          }
          self.loader = false;
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
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
            self.toast("toast_restored");
            self.getPages(self.currentStatus);
            return;
          }
          self.loader = false;
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
    });
  },
});
