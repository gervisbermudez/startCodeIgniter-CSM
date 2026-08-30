var AlbumsLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    albums: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: { item: {}, index: null },
    listEndpoint: "api/v1/albumes",
    listKey: "albums",
    listPk: "album_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterData: function () {
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.albums.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.albums;
    },
  },
  methods: {
    getPageImagePath: function (album, index) {
      var item = album.items && album.items[index] ? album.items[index] : { file: {} };
      if (item.file && item.file.file_front_path) {
        return BASEURL + item.file.file_front_path;
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getAlbums: function (page) {
      this.fetchList(page);
    },
    delete: function (album, index) {
      this.deleteListItem(album, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getAlbums();
    });
  },
});
