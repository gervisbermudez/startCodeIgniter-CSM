Vue.component("fileExplorerCollection", {
  template: "#fileExplorerCollection-template",
  data: function () {
    return {
      debug: DEBUGMODE,
      files: [],
    };
  },
  mixins: [mixins],
  computed: {
    shortFiles: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.files.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.files.filter((file) => {
          return file.file_type != "folder";
        }).slice(0, 25);
      }
    },
  },
  methods: {
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
        type: "POST",
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
      switch (filter) {
        case "important":
          this.getFilterFiles("featured", ["1"]);
          break;
        case "trash":
          this.getFilterFiles("file_path", ["./trash/"]);
          this.curDir = "./trash/";
          break;
        case "images":
          this.getFilterFiles("file_type", ["jpg", "png", "gif"]);
          break;
        case "doc":
          this.getFilterFiles("file_type", ["pdf", "doc"]);
          break;
        case "docs":
          this.getFilterFiles("file_type", ["pdf", "doc", "xls"]);
          break;
        case "audio":
          this.getFilterFiles("file_type", ["acc, mp3"]);
          break;
        case "video":
          this.getFilterFiles("file_type", ["mp4"]);
          break;
        case "zip":
          this.getFilterFiles("file_type", ["zip", "rar"]);
          break;
        default:
          break;
      }
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
      $(".fileExplorerCollection-root .collection").niceScroll();
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 2000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getFiles();
      this.debug ? console.log("mounted: fileExplorerCollection") : null;
    });
  },
});
