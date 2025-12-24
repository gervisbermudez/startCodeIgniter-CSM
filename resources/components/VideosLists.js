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
    modalid: 'deleteModal'
  },
  mixins: [mixins],
  computed: {
    filterAll: function () {
      console.log('[VideosLists] computed filterAll, videos:', this.videos, 'filter:', this.filter);
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        let filtered = this.videos.filter((value, index) => {
          let found = this.searchInObject(value, filterTerm);
          console.log('[VideosLists] searchInObject:', value, 'term:', filterTerm, 'found:', found);
          return found;
        });
        console.log('[VideosLists] filtered videos:', filtered);
        return filtered;
      }
      return this.videos;
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
    base_url: function(path) {
      return BASEURL + path;
    },
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

      console.log('[VideosLists] getVideos: sending AJAX', BASEURL + "api/v1/videos/", data);
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/videos/",
        data: data,
        dataType: "json",
        success: function (response) {
          console.log('[VideosLists] AJAX success', response);
          if (response && response.data) {
            var arr = Array.isArray(response.data) ? response.data : Object.values(response.data);
            self.videos = [].concat(arr);
            if (self.videos.length && typeof Vue !== 'undefined') {
              Vue.set(self, 'videos', self.videos);
            }
            console.log('[VideosLists] videos set:', self.videos, 'type:', Object.prototype.toString.call(self.videos), 'isArray:', Array.isArray(self.videos));
          } else {
            self.videos = [];
            console.log('[VideosLists] videos set to empty array');
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
            console.log('[VideosLists] after nextTick, videos:', self.videos, 'typeof:', typeof self.videos, 'isArray:', Array.isArray(self.videos));
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error('[VideosLists] AJAX error', error);
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
    tempDelete: function (item, index) {
      this.tempVideo.video = item;
      this.tempVideo.index = index;
    },
    confirmCallback: function (data) {
      if (data) {
        this.deleteVideo(this.tempVideo.video);
      }
    },
    confirmDelete(data) {
      if (data) {
        this.deleteVideo(this.tempVideo.video);
      }
    },
    resetFilter: function () {
      this.filter = "";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
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
      console.log('[VideosLists] mounted, this.videos:', this.videos, 'typeof:', typeof this.videos, 'isArray:', Array.isArray(this.videos));
      this.getVideos();
      this.initPlugins();
    });
  },
  });
