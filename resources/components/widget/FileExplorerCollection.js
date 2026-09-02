Vue.component("fileExplorerCollection", {
  template: "#fileExplorerCollection-template",
  props: ["files", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  computed: {
    shortFiles: function () {
      var files = (this.files || []).filter(function (file) {
        return file && file.file_type != "folder";
      });
      var self = this;
      files = files.slice().sort(function (a, b) {
        var ia = self.isImage(a) ? 0 : 1;
        var ib = self.isImage(b) ? 0 : 1;
        return ia - ib;
      });
      return files.slice(0, 12);
    },
  },
  methods: {
    isImage: function (file) {
      var t = file && file.file_type ? String(file.file_type).toLowerCase() : "";
      return ["jpg", "jpeg", "png", "gif", "webp", "svg", "bmp"].indexOf(t) !== -1;
    },
    fileIcon: function (file) {
      var t = file && file.file_type ? String(file.file_type).toLowerCase() : "";
      if (["pdf"].indexOf(t) !== -1) return "picture_as_pdf";
      if (["mp4", "mov", "avi", "webm"].indexOf(t) !== -1) return "movie";
      if (["mp3", "wav", "ogg", "aac"].indexOf(t) !== -1) return "audiotrack";
      if (["zip", "rar", "7z"].indexOf(t) !== -1) return "archive";
      if (t === "folder") return "folder";
      return "insert_drive_file";
    },
    getFiles() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            let data = response.data;
            data = data.map((file) => {
              return new ExplorerFile(file);
            });
            self.files = data;
            self.init();
          }
        },
      });
    },
    getFilterFiles(filter_name, filter_value) {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/filter_files",
        data: {
          filter_name: filter_name,
          filter_value: filter_value,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            let data = response.data.map((file) => {
              return new ExplorerFile(file);
            });
            self.files = data;
          }
        },
      });
    },
    filterFiles(filter) {
      var self = this;
      this.fetchLibraryFiles(
        { type: this.normalizeLibraryType(filter) },
        function (response) {
          if (response.code == 200) {
            var data = (response.data || []).map(function (file) {
              return new ExplorerFile(file);
            });
            self.files = data;
          }
        }
      );
    },
    featuredFileServe(file) {
      var self = this;
      file.featured = file.featured == "1" ? "0" : "1";
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/featured_file",
        data: {
          file: file,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Done!" });
          }
        },
      });
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 2000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.init();
      this.debug ? console.log("mounted: fileExplorerCollection") : null;
    });
  },
});
