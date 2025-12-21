var VideosLists = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    videos: [],
    loader: true,
    filter: "",
    tempVideo: {},
    currentStatus: null,
    modalid: 'deleteModal'
  },
  mixins: [mixins],
  computed: {
    filterAll: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.videos.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      }
      return this.videos;
    },
  },
  methods: {
    getVideoImagePath: function (video) {
      if (video.imagen_file && video.imagen_file.file_front_path) {
        return video.imagen_file.file_front_path;
      }
      if (video.preview && (video.preview.indexOf('http://') === 0 || video.preview.indexOf('https://') === 0)) {
        return video.preview;
      }
      if (video.preview) {
        return BASEURL + video.preview.replace(/^\/?/, '');
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getVideos: function (status = null) {
      var self = this;
      self.loader = true;
      self.currentStatus = status;
      var data = {};
      if (status !== null) {
        data.status = status;
      }

      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/videos/",
        data: data,
        dataType: "json",
        success: function (response) {
          if (response && response.data) {
            self.videos = response.data;
          } else {
            self.videos = [];
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
    deleteVideo: function (video) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/videos/" + (video.id || video.video_id),
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            const vidKey = video.id || video.video_id;
            self.videos = self.videos.filter(v => (v.id || v.video_id) !== vidKey);
            M.toast({ html: "Video eliminado" });
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
    setTempVideo: function (video, index) {
      this.tempVideo.video = video;
      this.tempVideo.index = index;
    },
    confirmDelete(data) {
      if (data) {
        this.deleteVideo(this.tempVideo.video);
      }
    },
    resetFilter: function () {
      this.filter = "";
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        if (elems && elems.length) M.Tooltip.init(elems, {});
        var dd = document.querySelectorAll(".dropdown-trigger");
        if (dd && dd.length) M.Dropdown.init(dd, {});
        var modalElems = document.querySelectorAll('.modal');
        if (modalElems && modalElems.length) M.Modal.init(modalElems, {});
      }, 300);
    },
    hasPermission: function (perm) {
      // Simplified permission helper - adjust if your app uses different keys
      try {
        return typeof has_permisions === 'function' ? has_permisions(perm) : true;
      } catch (e) {
        return true;
      }
    }
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getVideos();
      this.initPlugins();
    });
  },
});
