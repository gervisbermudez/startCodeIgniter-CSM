var AlbumsLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    albums: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {
      item: {},
      index: null,
    },
  },
  mixins: [mixins],
  computed: {
    filterData: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.albums.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.albums;
      }
    },
  },
  methods: {
    getcontentText: function (content) {
      if (!content) return "";
      var span = document.createElement("span");
      span.innerHTML = content;
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
    getPageImagePath(album, index) {
      let item = album.items[index] ? album.items[index] : { file: {} };
      if (item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getAlbums: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/albumes/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.albums = response.data;
          self.albums = self.albums.map((element) => {
            if (element.user) {
              element.user = new User(element.user);
            }
            return element;
          });
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
    delete: function (album, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/albumes/" + album.album_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.albums.splice(index, 1);
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
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.delete(this.toDeleteItem.item, this.toDeleteItem.index);
      }
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
      this.getAlbums();
      this.initPlugins();
    });
  },
});
