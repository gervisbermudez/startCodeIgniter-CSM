var fileExplorerModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    root: "./",
    curDir: "",
    loader: false,
    fileloader: false,
    files: [],
    recentlyFiles: [],
    backto: null,
    search: "",
    editFile: {},
    moveToTrash: {},
    showSideRightBar: false,
    sideRightBarSelectedFile: {},
  },
  mixins: [mixins],
  computed: {
    getFolders() {
      return this.files.filter((fileobject) => {
        return fileobject.file_type == "folder";
      });
    },
    getFiles() {
      return this.files.filter((fileobject) => {
        return fileobject.file_type != "folder";
      });
    },
    getBackPath() {
      if (this.getbreadcrumb.length > 0) {
        return this.getbreadcrumb[this.getbreadcrumb.length - 1].path;
      } else {
        return this.root;
      }
    },
    getbreadcrumb() {
      let breadcrumb = this.curDir.split("/").filter((value) => {
        return value != "" && value != ".";
      });
      breadcrumb = breadcrumb.map((element, index) => {
        let tempArray = [];
        for (let i = 0; i < index; i++) {
          tempArray.push(breadcrumb[i]);
        }

        let path = tempArray.join("/");
        if (path) {
          return {
            path: this.root + path + "/",
            folder: element,
          };
        } else {
          return {
            path: this.root + path,
            folder: element,
          };
        }
      });
      return breadcrumb;
    },
  },
  watch: {
    curDir: function (value) {
      if ($("#input-100").data("fileinput")) {
        $("#input-100").data("fileinput").uploadExtraData.curDir = value;
      }
    },
  },
  methods: {
    getFullFileName(file) {
      return file.file_name + "." + file.file_type;
    },
    setSideRightBarSelectedFile(file) {
      (url = BASEURL + "api/v1/users/" + file.user_id),
        (this.sideRightBarSelectedFile = file);
      this.showSideRightBar = true;
      this.sideRightBarSelectedFile.user = null;
      this.sideRightBarSelectedFile.user_group = null;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          if (response.code == 200) {
            this.sideRightBarSelectedFile.user = new User(response.data);
            this.$forceUpdate();
          }
          this.initMT();
        })
        .catch((error) => {
          console.error(error);
        });
      url = BASEURL + "/api/v1/users/usergroups/" + file.shared_user_group_id;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          this.sideRightBarSelectedFile.user_group = response.data;
          this.$forceUpdate();
        })
        .catch((error) => {
          console.error(error);
        });
    },
    setCloseSideRightBar() {
      this.showSideRightBar = false;
    },
    getFullFilePath(file) {
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    getIcon(fileObject) {
      let icon = "far fa-file";
      switch (fileObject.file_type) {
        case "folder":
          icon = "far fa-folder";
          break;
        case "jpg":
        case "png":
        case "gif":
          icon = "fas fa-file-image";
          break;
        case "html":
          icon = "fab fa-html5";
          break;
        case "scss":
          icon = "fab fa-sass";
          break;
        case "css":
        case "min.css":
          icon = "fab fa-css3-alt";
          break;
        case "txt":
          icon = "far fa-file-alt";
          break;
        case "php":
        case "blade.php":
          icon = "fab fa-php";
          break;
        case "js":
        case "json":
        case "min.js":
          icon = "fab fa-js";
          break;
        case "eot":
        case "otf":
        case "woff2":
          icon = "fas fa-font";
          break;
      }
      return icon;
    },
    getExtention(fileObject) {
      if (fileObject.file_type == "folder") {
        return "";
      } else {
        return "." + fileObject.file_type;
      }
    },
    isImage(file) {
      if (
        file.file_type == "jpg" ||
        file.file_type == "png" ||
        file.file_type == "gif"
      ) {
        return true;
      }
      return false;
    },
    renameFile(file) {
      console.log(file);
      this.editFile = file;
      this.editFile.new_name = this.editFile.file_name;
    },
    renameFileServe() {
      var self = this;

      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_rename_file",
        data: {
          file: self.editFile,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.editFile.file_name = self.editFile.new_name;
            M.toast({ html: "Renamed file" });
            var elems = document.querySelectorAll(".modal");
            var instances = M.Modal.init(elems, {});
            instances.close();
          }
        },
      });
    },
    featuredFileServe(file) {
      var self = this;
      file.featured = file.featured == 1 ? 0 : 1;
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
            self.$forceUpdate();
          }
        },
      });
    },
    trashFile(file) {
      this.moveToTrash = file;
    },
    moveFileTo(file, newPath) {
      var self = this;
      var file = {};
      var indexFile;
      self.files.forEach((element, index) => {
        if (element.rand_key == file.rand_key) {
          file = self.files[index];
          indexFile = index;
        }
      });
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_move_file",
        data: {
          file: file,
          newPath: newPath,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            html = "<span>Done! </span>";
            M.toast({ html: html });

            self.files[indexFile].file_path = newPath;
            self.files.splice(indexFile, 1);
          }
        },
      });
    },
    getImagePath(file) {
      if (this.isImage(file)) {
        return (
          BASEURL +
          file.file_path.substr(2) +
          file.file_name +
          "." +
          file.file_type
        );
      }
    },
    reloadFileExplorer() {
      var self = this;
      self.fileloader = true;
      self.files = [];
      var url = BASEURL + "api/v1/files/reload_file_explorer";
      $.ajax({
        type: "POST",
        url: url,
        data: {
          path: self.root,
        },
        dataType: "json",
        success: function (response) {
          self.navigateFiles(self.curDir);
        },
      });
    },
    toggleView() {},
    navigateFiles(path) {
      var self = this;
      self.backto = self.getBackPath;
      self.curDir = path;
      if (path == self.root) {
        self.backto = null;
      }
      self.fileloader = true;
      var params = {
        path: path,
      };
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: params,
        dataType: "json",
        success: function (response) {
          self.fileloader = false;
          if (response.code == 200 && response.data.length) {
            self.files = response.data;
          } else {
            self.files = [];
          }
          self.initMT();
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
    resetSearch() {
      this.search = null;
      this.navigateFiles(this.root);
    },
    searchfiles() {
      if (this.search) {
        this.getFilterFiles("file_name", [this.search]);
      } else {
        this.navigateFiles(this.root);
      }
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
            self.files = response.data;
          }
        },
      });
    },
    initMT() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".modal");
        var instances = M.Modal.init(elems, {});
        var instance = M.Tabs.init(document.getElementById("filetabs"), {});
      }, 2000);
    },
  },
  filters: {
    shortName: function (value) {
      if (!value) return "";
      value = value.toString();
      if (value.length > 15) {
        return value.substr(0, 15) + "...";
      } else {
        return value.substr(0, 15);
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      var self = this;
      this.navigateFiles(self.root);
    });
  },
});
