Vue.component("FileExplorerSelector", {
  template: "#file-explorar-selector",
  props: [
    "mode",
    "multiple",
    "filter",
    "modal",
    "ignore",
    "preselected",
    "uploader",
    "initialdir",
  ], //mode Can be "all", "files", "folders"
  data: function () {
    return {
      debug: DEBUGMODE,
      title: "Seleccionar",
      selectedRoot: false,
      root: "./",
      curDir: "",
      fileloader: false,
      files: [],
      backto: null,
      search: "",
      create_folder_process: false,
      new_folder_name: "new folder",
      input_id: this.makeid(15),
    };
  },
  mixins: [mixins],
  watch: {
    initialdir: function (newVal) {
      this.curDir = newVal;
    },
  },
  computed: {
    selected() {
      this.files;
      return this.files.filter((file) => {
        return file.selected == true;
      });
    },
    getFolders() {
      return this.files.filter((file) => {
        if (file.file_path == "./" && file.file_name == "trash") {
          return false;
        }
        this.preselected.forEach((element) => {
          if (file.file_id == element.file_id) file.selected = true;
        });
        return file.file_type == "folder";
      });
    },
    getFiles() {
      return this.files.filter((file) => {
        this.preselected.forEach((element) => {
          if (file.file_id == element.file_id) file.selected = true;
        });
        return file.file_type != "folder";
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
  methods: {
    makeNewFolder() {
      this.create_folder_process = true;
      setTimeout(() => {
        $(`#folder_name` + this.modal).focus();
        $(`#folder_name` + this.modal).select();
      }, 1000);
    },
    makeFolderServer() {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/make_dir",
        data: {
          path: self.curDir,
          new_folder_name: $(`#folder_name` + this.modal).val(),
        },
        dataType: "json",
        success: function (response) {
          console.log(response);
          self.create_folder_process = false;
          self.navigateFiles(self.curDir);
        },
      });
    },
    showCheckbox(file) {
      if (
        this.mode == "folders" && //Only folders
        !this.multiple && //Single folder selected
        this.selected.length > 0 && //There are almost one folder seleted
        file.file_id != this.selected[0].file_id //The current file is not the selected
      ) {
        false;
      } else if (
        this.mode == "folders" && //Only folders
        !this.multiple && //Single folder selected
        this.selected.length > 0 && //There are almost one folder seleted
        file.file_id == this.selected[0].file_id //The current file is the selected
      ) {
        true;
      }
      if (this.multiple || this.selected.length == 0) {
        return true;
      }

      if (file.file_id == this.selected[0].file_id) {
        return true;
      } else {
        return false;
      }
    },
    getSelected() {
      return this.selected;
    },
    getSelectedRoot() {
      return {
        is_root: true,
        file_id: "0",
        rand_key: "",
        file_name: "",
        file_path: "./",
        file_type: "",
        parent_name: "./",
        user_id: "",
        shared_user_group_id: "",
        share_link: "",
        featured: "",
        date_create: "",
        date_update: "",
        status: "",
        selected: true,
      };
    },
    onClickButton(event) {
      let files = this.getSelected();
      this.$emit(
        "notify",
        this.selectedRoot ? [this.getSelectedRoot()] : files
      );
      this.files = this.files.map((file) => {
        file.selected = false;
        return file;
      });
    },
    getFullFileName(file) {
      return file.file_name + "." + file.file_type;
    },
    getFullFilePath(file) {
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    getIcon(file) {
      let icon = "far fa-file";
      switch (file.file_type) {
        case "folder":
          icon = "far fa-folder";
          break;
        case "jpg":
        case "jpeg":
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
    getExtention(file) {
      if (file.file_type == "folder") {
        return "";
      } else {
        return "." + file.file_type;
      }
    },
    isImage(file) {
      if (
        file.file_type == "jpg" ||
        file.file_type == "jpeg" ||
        file.file_type == "png" ||
        file.file_type == "gif"
      ) {
        return true;
      }
      return false;
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
          self.init();
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
      var self = this;
      if (self.filter) {
        this.filterFiles(self.filter);
      } else {
        this.navigateFiles(self.root);
      }
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
        type: "GET",
        url: BASEURL + "api/v1/files/filter_files",
        data: {
          filter_name: filter_name,
          filter_value: filter_value,
        },
        dataType: "json",
        success: (response) => {
          if (response.code == 200) {
            self.files = response.data;
            self.init();
          }
        },
        error: (error) => {
          console.log({ error });
        },
      });
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".materialboxed");
        M.Materialbox.init(elems, {});
        M.Tabs.init(document.getElementById("selectorTabs_" + this.modal), {});
      }, 3000);
    },
    initUploader() {
      $(`#${this.input_id}`)
        .fileinput({
          uploadUrl: BASEURL + "admin/archivos/ajax_upload_file",
          enableResumableUpload: true,
          resumableUploadOptions: {
            // uncomment below if you wish to test the file for previous partial uploaded chunks
            // to the server and resume uploads from that point afterwards
            // testUrl: "http://localhost/test-upload.php"
          },
          uploadExtraData: {
            uploadToken: "SOME-TOKEN", // for access control / security
            curDir: this.curDir || "./uploads/",
          },
          showCancel: true,
          initialPreview: [],
          fileActionSettings: {
            showRemove: true,
            showUpload: true,
            showDownload: true,
            showZoom: false,
            showDrag: true,
            removeIcon: '<i class="fas fa-trash"></i>',
            removeClass: "btn btn-sm btn-kv btn-default btn-outline-secondary",
            removeErrorClass: "btn btn-sm btn-kv btn-danger",
            removeTitle: "Remove file",
            uploadIcon: '<i class="fas fa-upload"></i>',
            uploadClass: "btn btn-sm btn-kv btn-default btn-outline-secondary",
            uploadTitle: "Upload file",
            uploadRetryIcon: '<i class="glyphicon glyphicon-repeat"></i>',
            uploadRetryTitle: "Retry upload",
            downloadIcon: '<i class="fas fa-download"></i>',
            downloadClass:
              "btn btn-sm btn-kv btn-default btn-outline-secondary",
            downloadTitle: "Download file",
            zoomIcon: '<i class="fas fa-search-plus"></i>',
            zoomClass: "btn btn-sm btn-kv btn-default btn-outline-secondary",
            zoomTitle: "View Details",
            dragIcon: '<i class="fas fa-arrows-alt"></i>',
            dragClass: "text-info",
            dragTitle: "Move / Rearrange",
            dragSettings: {},
            indicatorNew:
              '<i class="glyphicon glyphicon-plus-sign text-warning"></i>',
            indicatorSuccess:
              '<i class="glyphicon glyphicon-ok-sign text-success"></i>',
            indicatorError:
              '<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>',
            indicatorLoading:
              '<i class="glyphicon glyphicon-hourglass text-muted"></i>',
            indicatorPaused:
              '<i class="glyphicon glyphicon-pause text-primary"></i>',
            indicatorNewTitle: "Not uploaded yet",
            indicatorSuccessTitle: "Uploaded",
            indicatorErrorTitle: "Upload Error",
            indicatorLoadingTitle: "Uploading ...",
            indicatorPausedTitle: "Upload Paused",
          },
          uploadIcon: '<i class="fas fa-upload"></i>',
          removeIcon: '<i class="fas fa-trash"></i>',
          overwriteInitial: false,
          // initialPreview: [],          // if you have previously uploaded preview files
          // initialPreviewConfig: [],    // if you have previously uploaded preview files
          deleteUrl: "",
          progressClass:
            "determinate progress-bar bg-success progress-bar-success progress-bar-striped active",
          progressInfoClass:
            "determinate progress-bar bg-info progress-bar-info progress-bar-striped active",
          progressCompleteClass:
            "determinate progress-bar bg-success progress-bar-success",
          progressPauseClass:
            "determinate progress-bar bg-primary progress-bar-primary progress-bar-striped active",
          progressErrorClass:
            "determinate progress-bar bg-danger progress-bar-danger",
        })
        .on("fileuploaded", (event, previewId, index, fileId) => {
          let instance = M.Tabs.init(
            document.getElementById("selectorTabs_" + this.modal),
            {}
          );
          instance.select("selector");
          this.updateSelector();
        });
    },
    destroyFileinputInstance() {
      $(`#${this.input_id}`).fileinput("destroy");
    },
    updateSelector() {
      if (!this.preselected) {
        this.preselected = [];
      }
      if (this.filter && this.mode == "files") {
        this.filterFiles(this.filter);
      } else {
        this.navigateFiles(this.root);
      }
      this.init();
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
      this.updateSelector();
    });
  },
});
