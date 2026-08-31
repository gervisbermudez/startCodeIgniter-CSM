var AlbumsItemsLists = new Vue({
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
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.album.items.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.album.items;
    },
  },
  methods: {
    getPageImagePath: function (item) {
      if (item.file && item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "/public/img/default.jpg";
    },
    copyCallcack: function (selected) {},
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/albumes/" + self.album_id,
        data: {},
        dataType: "json",
        success: function (response) {
          self.album = response.data;
          if (self.album.user) {
            self.album.user = new User(self.album.user);
          }
          if (!self.album.items) {
            self.album.items = [];
          }
          self.loader = false;
          self.initPlugins();
        },
        error: function (xhr) {
          self.loader = false;
          self.toastError(xhr);
        },
      });
    },
    deletePage: function (item, index) {
      var self = this;
      if (!item || !item.album_item_id) {
        return;
      }
      self.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/albumes/delete_album_item/" + item.album_item_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.album.items.splice(index, 1);
            self.toast("toast_deleted");
          } else {
            self.toastError(null, response);
          }
          self.loader = false;
          self.initPlugins();
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
      this.album_id = window.location.pathname.split("/").pop();
      this.getPages();
    });
  },
});
