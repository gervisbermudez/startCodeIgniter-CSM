var fileUploaderModule = new Vue({
  el: "#fileUploader",
  data: {
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
    multiple: true,
    allowed_files: null,
    doneSelection: false,
    preventLoadFilesOnLoad: true,
  },
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
    finishedSelection() {
      fileUploaderModule.callBakSelectedImagen(this.getSelectedFiles());
    },
    getSelectedFiles() {
      return this.files.filter((item, index) => {
        return !!item.isSelected;
      });
    },
    setSelectedFile(item) {
      if (!this.multiple) {
        this.getFiles.map((file, index) => {
          file.isSelected = false;
        });
      }
      item["isSelected"] = !item["isSelected"];
      this.doneSelection = this.getSelectedFiles().length > 0;
      this.$forceUpdate();
    },
    getFullFileName(item) {
      return item.file_name + "." + item.file_type;
    },
    getFullFilePath(item) {
      return BASEURL + item.file_path + this.getFullFileName(item);
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
    isImage(item) {
      if (
        item.file_type == "jpg" ||
        item.file_type == "png" ||
        item.file_type == "gif"
      ) {
        return true;
      }
      return false;
    },
    renameFile(item) {
      console.log(item);
      this.editFile = item;
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
    featuredFileServe(item) {
      var self = this;
      var file = {};
      self.files.forEach((element, index) => {
        if (element.rand_key == item.rand_key) {
          self.files[index].featured =
            self.files[index].featured == "1" ? "0" : "1";
          file = self.files[index];
        }
      });
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
          }
        },
      });
    },
    trashFile(item) {
      this.moveToTrash = item;
    },
    moveFileTo(item, newPath) {
      var self = this;
      var file = {};
      var indexFile;
      self.files.forEach((element, index) => {
        if (element.rand_key == item.rand_key) {
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
    copyFileTo(item, newPath, callBack) {
      var self = this;
      var file = {};
      var indexFile;
      self.files.forEach((element, index) => {
        if (element.rand_key == item.rand_key) {
          file = self.files[index];
          indexFile = index;
        }
      });
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_copy_file",
        data: {
          file: file,
          newPath: newPath,
        },
        dataType: "json",
        success: function (response) {
          if (typeof callBack == "function") {
            callBack(response);
          } else {
            if (response.code == 200) {
              html = "<span>Done! </span>";
              M.toast({ html: html });
              self.files[indexFile].file_path = newPath;
              self.files.splice(indexFile, 1);
            }
          }
        },
      });
    },
    getImagePath(item) {
      if (this.isImage(item)) {
        return (
          BASEURL +
          item.file_path.substr(2) +
          item.file_name +
          "." +
          item.file_type
        );
      }
    },
    navigateFiles(path) {
      var self = this;
      self.backto = self.getBackPath;
      self.curDir = path;
      if (path == self.root) {
        self.backto = null;
      }
      self.fileloader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/archivos/ajax_get_files",
        data: {
          path: path,
        },
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
        url: BASEURL + "admin/archivos/ajax_get_filter_files",
        data: {
          filter_name: filter_name,
          filter_value: filter_value,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.files = response.data;
            self.initMT();
          }
        },
      });
    },
    initMT() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
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
      if (!self.preventLoadFilesOnLoad) {
        this.navigateFiles(self.root);
      }
    });
  },
});
