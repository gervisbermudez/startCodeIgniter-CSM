var AlbumsLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    tableView: false,
    loader: true,
    filter: "",
    album_id: null,
    album: {
      album_id: "",
      date_create: "",
      date_publish: "",
      date_update: "",
      description: "",
      model_type: "",
      name: "",
      status: "",
      user: new User(),
      user_id: "",
      items: [],
    },
  },
  mixins: [mixins],
  computed: {
    filterData: function () {
      if (!!this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.album.items.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.album.items;
      }
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
      if (item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "public/img/default.jpg";
    },
    copyCallcack(selected) {
      console.log(selected);
    },
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/albumes/" + self.album_id,
        data: {},
        dataType: "json",
        success: function (response) {
          self.album = response.data;
          self.album.user = new User(self.album.user);
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    deletePage: function (item, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/albumes/" + item.page_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.items.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
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
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.album_id = window.location.pathname.split("/").pop();
      this.getPages();
      this.initPlugins();
    });
  },
  updated: function () {
    this.$nextTick(function () {
      // Code that will run only after the
      // entire view has been re-rendered
      this.initPlugins();
    })
  }
});
