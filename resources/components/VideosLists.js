var VideosLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    videos: [],
    loader: true,
    filter: "",
    tempVideo: {},
    tableView: false,
    currentStatus: null,
    modalid: "deleteModal",
    listEndpoint: "api/v1/videos",
    listKey: "videos",
    listPk: "video_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterAll: function () {
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.videos.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.videos;
    },
  },
  methods: {
    listExtraQuery: function () {
      if (this.currentStatus !== null) {
        return { status: this.currentStatus };
      }
      return {};
    },
    getVideoImagePath: function (video) {
      if (video.imagen_file && video.imagen_file.file_front_path) {
        return video.imagen_file.file_front_path;
      }
      if (video.preview && (video.preview.indexOf("http://") === 0 || video.preview.indexOf("https://") === 0)) {
        return video.preview;
      }
      if (video.preview) {
        return BASEURL + video.preview.replace(/^\/?/, "");
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getVideos: function (status) {
      if (typeof status !== "undefined") {
        this.currentStatus = status;
      }
      this.fetchList();
    },
    deleteVideo: function (video, index) {
      this.deleteListItem(video, index);
    },
    setTempVideo: function (video, index) {
      this.tempVideo.video = video;
      this.tempVideo.index = index;
    },
    tempDelete: function (item, index) {
      this.tempVideo.video = item;
      this.tempVideo.index = index;
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmDelete: function (data) {
      if (data) {
        this.deleteListItem(this.tempVideo.video, this.tempVideo.index);
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getVideos();
    });
  },
});
