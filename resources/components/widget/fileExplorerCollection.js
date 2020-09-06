Vue.component("fileExplorerCollection", {
  template: "#fileExplorerCollection-template",
  data: function () {
    return {
      debug: DEBUGMODE,
      files: [],
    };
  },
  methods: {
    getFeaturedFiles() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_get_filter_files",
        data: {
          filter_name: "featured",
          filter_value: "1",
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            let data = response.data;
            data = data.map((file) => {
              return new ExplorerFile(file);
            });
            self.files = data;
          }
        },
      });
    },
    featuredFileServe(file) {
      var self = this;
      file.featured = 0;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_featured_file",
        data: {
          file: file,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Done!" });
            self.getFeaturedFiles();
          }
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getFeaturedFiles();
      this.debug ? console.log("mounted: usersCollection") : null;
    });
  },
});
