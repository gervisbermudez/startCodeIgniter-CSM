var FileExplorerModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    root: "./uploads/",
    curDir: "./uploads/",
    loader: false,
    fileloader: false,
    files: [],
    recentlyFiles: [],
    backto: null,
    search: "",
    searchTimer: null,
    editFile: {},
    moveToTrash: {},
    showSideRightBar: false,
    sideRightBarSelectedFile: {},
    fileToMove: {},
    activeFilter: null,
    selectedIds: [],
    creatingFolder: false,
    newFolderName: "",
    savingContent: false,
    listView: false,
    replaceTarget: null,
    showFile: {
      file_name: "",
      file_type: "",
      file_content: "",
    },
  },
  mixins: [mixins],
  computed: {
    getFolders: function () {
      var self = this;
      return this.files.filter(function (file) {
        if (file.file_path == "./" && file.file_name == "trash") {
          return false;
        }
        if (
          file.file_path == self.root &&
          (file.file_name == "trash" || file.file_name == ".")
        ) {
          return false;
        }
        return file.file_type == "folder";
      });
    },
    getFiles: function () {
      return this.files.filter(function (file) {
        return file.file_type != "folder";
      });
    },
    inThemes: function () {
      return this.curDir.indexOf("./themes/") === 0;
    },
    inTrash: function () {
      return (
        this.curDir.indexOf("/trash/") !== -1 || this.activeFilter === "trash"
      );
    },
    selectedCount: function () {
      return this.selectedIds.length;
    },
    getBackPath: function () {
      if (this.getbreadcrumb.length > 1) {
        return this.getbreadcrumb[this.getbreadcrumb.length - 2].path;
      }
      return this.root;
    },
    getbreadcrumb: function () {
      var breadcrumb = this.curDir.split("/").filter(function (value) {
        return value != "" && value != ".";
      });
      return breadcrumb.map(function (element, index) {
        var parts = breadcrumb.slice(0, index + 1);
        return {
          path: "./" + parts.join("/") + "/",
          folder: element,
        };
      });
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
    isSelected: function (file) {
      return this.selectedIds.indexOf(file.file_id) !== -1;
    },
    toggleSelect: function (file, event) {
      if (event) {
        event.stopPropagation();
      }
      var id = file.file_id;
      if (!id) {
        return;
      }
      var idx = this.selectedIds.indexOf(id);
      if (idx === -1) {
        this.selectedIds.push(id);
      } else {
        this.selectedIds.splice(idx, 1);
      }
    },
    selectAllVisible: function () {
      var ids = this.getFiles
        .map(function (f) {
          return f.file_id;
        })
        .concat(
          this.getFolders.map(function (f) {
            return f.file_id;
          })
        )
        .filter(function (id) {
          return !!id;
        });
      this.selectedIds = ids;
    },
    clearSelection: function () {
      this.selectedIds = [];
    },
    selectedFileObjects: function () {
      var ids = this.selectedIds;
      return this.files.filter(function (file) {
        return ids.indexOf(file.file_id) !== -1;
      });
    },
    getFullFileName: function (file) {
      return file.file_name + "." + file.file_type;
    },
    setSideRightBarSelectedFile: function (file) {
      var self = this;
      window.scrollTo(0, 0);
      this.sideRightBarSelectedFile = file;
      this.showSideRightBar = true;
      this.sideRightBarSelectedFile.user = null;
      this.sideRightBarSelectedFile.user_group = null;
      this.showFile = {
        file_name: file.file_name,
        file_type: file.file_type,
        file_content: "",
      };
      if (this.fileIsImage(file)) {
        this.showFile = Object.assign({}, file, {
          file_content: true,
          isImagen: true,
        });
      } else if (this.fileIsText(file)) {
        this.getFileContent(file);
      }
      if (!file.file_id) {
        return;
      }
      fetch(BASEURL + "api/v1/files/" + file.file_id)
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          if (response.code == 200) {
            var history = response.data.history || [];
            self.sideRightBarSelectedFile.history = history
              .slice()
              .reverse()
              .map(function (e) {
                return Object.assign({}, e, {
                  user: new User(e.user),
                });
              });
          }
        })
        .catch(function (error) {
          console.error(error);
        });
      fetch(BASEURL + "api/v1/users/" + file.user_id)
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          if (response.code == 200) {
            self.sideRightBarSelectedFile.user = new User(response.data);
            self.$forceUpdate();
          }
          self.initMT();
        })
        .catch(function (error) {
          console.error(error);
        });
      fetch(BASEURL + "api/v1/users/usergroups/" + file.shared_user_group_id)
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          self.sideRightBarSelectedFile.user_group = response.data;
          self.$forceUpdate();
        })
        .catch(function (error) {
          console.error(error);
        });
    },
    getFileHistoryIcon: function (action) {
      switch (action) {
        case "upload":
          return "cloud_upload";
        case "rename":
        case "edit":
        case "replace":
          return "edit";
        case "move":
          return "content_cut";
        case "copy":
          return "content_copy";
        default:
          return "folder";
      }
    },
    setCloseSideRightBar: function () {
      this.showSideRightBar = false;
      this.showFile = { file_name: "", file_type: "", file_content: "" };
    },
    getFullFilePath: function (file) {
      return BASEURL + file.file_path + this.getFullFileName(file);
    },
    copyFileLink: function (file) {
      var self = this;
      var url = this.getFullFilePath(file);
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
          self.toast("files_link_copied");
        });
        return;
      }
      this.shareFile(file);
    },
    shareFile: async function (file) {
      var path = this.getFullFilePath(file);
      try {
        await navigator.share({ title: file.file_name, text: "", url: path });
      } catch (err) {
        this.copyFileLink(file);
      }
    },
    downloadFile: function (file) {
      if (!file.file_id) {
        return;
      }
      window.location.href = BASEURL + "api/v1/files/download/" + file.file_id;
    },
    downloadZip: function (ids) {
      var self = this;
      var fileIds = ids && ids.length ? ids : this.selectedIds;
      if (!fileIds.length) {
        return;
      }
      var body = fileIds
        .map(function (id) {
          return "file_ids[]=" + encodeURIComponent(id);
        })
        .join("&");
      fetch(BASEURL + "api/v1/files/download_zip", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        credentials: "same-origin",
        body: body,
      })
        .then(function (res) {
          var ctype = res.headers.get("Content-Type") || "";
          if (!res.ok || ctype.indexOf("json") !== -1) {
            throw new Error("zip");
          }
          return res.blob();
        })
        .then(function (blob) {
          var a = document.createElement("a");
          a.href = URL.createObjectURL(blob);
          a.download = "files.zip";
          document.body.appendChild(a);
          a.click();
          a.remove();
        })
        .catch(function () {
          self.toast("files_zip_error");
        });
    },
    getIcon: function (file) {
      var icon = "far fa-file";
      switch (file.file_type) {
        case "folder":
          icon = "far fa-folder";
          break;
        case "jpg":
        case "jpeg":
        case "png":
        case "gif":
        case "webp":
        case "svg":
        case "bmp":
          icon = "fas fa-file-image";
          break;
        case "html":
          icon = "fab fa-html5";
          break;
        case "scss":
          icon = "fab fa-sass";
          break;
        case "css":
          icon = "fab fa-css3-alt";
          break;
        case "txt":
        case "md":
          icon = "far fa-file-alt";
          break;
        case "php":
          icon = "fab fa-php";
          break;
        case "js":
          icon = "fab fa-js";
          break;
        case "json":
          icon = "far fa-file-code";
          break;
        case "pdf":
          icon = "far fa-file-pdf";
          break;
        case "zip":
        case "rar":
        case "7z":
        case "gz":
          icon = "far fa-file-archive";
          break;
        case "mp3":
        case "aac":
        case "wav":
          icon = "far fa-file-audio";
          break;
        case "mp4":
        case "webm":
        case "mov":
          icon = "far fa-file-video";
          break;
      }
      return icon;
    },
    getExtention: function (file) {
      if (!file || file.file_type == "folder") {
        return "";
      }
      return "." + file.file_type;
    },
    isImage: function (file) {
      return this.fileIsImage(file);
    },
    renameFile: function (file) {
      this.editFile = Object.assign({}, file);
      this.editFile.new_name = file.file_name;
    },
    renameFileServe: function () {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/rename_file",
        data: { file: self.editFile },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.editFile.file_name = self.editFile.new_name;
            self.toast("files_renamed");
            var modalEl = document.getElementById("modal1");
            if (modalEl && M.Modal.getInstance(modalEl)) {
              M.Modal.getInstance(modalEl).close();
            }
            self.refreshCurrent();
          } else {
            self.toastError(null, response);
          }
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    featuredFileServe: function (file) {
      var self = this;
      file.featured = file.featured == 1 ? 0 : 1;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/featured_file",
        data: { file: file },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("toast_done");
            self.$forceUpdate();
          }
        },
      });
    },
    setFileToMove: function (file) {
      this.fileToMove = file;
    },
    trashFile: function (file) {
      this.moveToTrash = file;
    },
    moveCallcack: function (selected) {
      var instance = M.Modal.getInstance(
        document.getElementById("folderSelectorMove")
      );
      if (instance) {
        instance.close();
      }
      if (!selected || !selected[0]) {
        return;
      }
      var dest =
        selected[0].file_path +
        (selected[0].file_name ? selected[0].file_name + "/" : "");
      var targets = this.fileToMove.file_id
        ? [this.fileToMove]
        : this.selectedFileObjects();
      var self = this;
      targets.forEach(function (item) {
        self.moveFileTo(item, dest);
      });
    },
    copyCallcack: function (selected) {
      var instance = M.Modal.getInstance(
        document.getElementById("folderSelectorCopy")
      );
      if (instance) {
        instance.close();
      }
      if (!selected || !selected[0]) {
        return;
      }
      this.copyFileTo(
        this.fileToMove,
        selected[0].file_path +
          (selected[0].file_name ? selected[0].file_name + "/" : "")
      );
    },
    deleteFile: function (file) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/delete/" + file.file_id,
        data: { file: file },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("files_deleted");
            self.refreshCurrent();
          }
        },
      });
    },
    moveFileTo: function (file, newPath) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/move_file",
        data: { file: file, newPath: newPath },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast(
              newPath.indexOf("trash") !== -1
                ? "files_moved_trash"
                : "files_moved"
            );
            self.files = self.files.filter(function (item) {
              return item.file_id != file.file_id;
            });
            self.clearSelection();
          } else {
            self.toastError(null, response);
          }
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    copyFileTo: function (file, newPath) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/copy_file",
        data: { file: file, newPath: newPath },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("files_copied");
            self.refreshCurrent();
          }
        },
      });
    },
    getImagePath: function (file) {
      if (this.isImage(file) && file.file_path) {
        return (
          BASEURL +
          String(file.file_path).replace(/^\.\//, "") +
          file.file_name +
          "." +
          file.file_type
        );
      }
    },
    reloadFileExplorer: function () {
      var self = this;
      self.fileloader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/reload_file_explorer",
        data: { path: self.root },
        dataType: "json",
        timeout: 120000,
        success: function () {
          self.refreshCurrent();
        },
        error: function (xhr) {
          self.fileloader = false;
          self.toastError(xhr);
          self.refreshCurrent();
        },
      });
    },
    refreshCurrent: function () {
      if (this.activeFilter) {
        this.filterFiles(this.activeFilter);
        return;
      }
      this.navigateFiles(this.curDir);
    },
    navigateFiles: function (path) {
      var self = this;
      path = path || this.root;
      self.activeFilter = null;
      self.backto = path === self.root ? null : self.getBackPath;
      self.curDir = path;
      self.fileloader = true;
      var newurl =
        window.location.protocol +
        "//" +
        window.location.host +
        window.location.pathname +
        "?path=" +
        encodeURIComponent(path);
      window.history.pushState({ path: newurl }, "", newurl);
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/",
        data: { path: path },
        dataType: "json",
        success: function (response) {
          self.fileloader = false;
          if (response.code == 200) {
            self.files = response.data || [];
          } else {
            self.files = [];
          }
          self.initMT();
        },
        error: function (xhr) {
          self.fileloader = false;
          self.files = [];
          self.toastError(xhr);
        },
      });
    },
    filterFiles: function (filter) {
      var self = this;
      this.activeFilter = this.normalizeLibraryType(filter);
      if (this.activeFilter === "trash") {
        this.curDir = this.libraryTrashPath();
      }
      this.fileloader = true;
      this.fetchLibraryFiles(
        { type: this.activeFilter, q: this.search || "" },
        function (response) {
          self.fileloader = false;
          if (response.code == 200) {
            self.files = response.data || [];
          } else {
            self.files = [];
          }
          self.initMT();
        },
        function (xhr) {
          self.fileloader = false;
          self.toastError(xhr);
        }
      );
    },
    resetSearch: function () {
      this.search = "";
      this.refreshCurrent();
    },
    searchfiles: function () {
      var self = this;
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(function () {
        if (self.search) {
          self.fileloader = true;
          self.fetchLibraryFiles(
            { type: self.activeFilter || "", q: self.search },
            function (response) {
              self.fileloader = false;
              self.files = response.code == 200 ? response.data || [] : [];
              self.initMT();
            }
          );
        } else {
          self.refreshCurrent();
        }
      }, 300);
    },
    loadRecentsStrip: function () {
      var self = this;
      this.fetchLibraryFiles({ type: "recent" }, function (response) {
        if (response.code == 200) {
          self.recentlyFiles = (response.data || []).slice(0, 8);
          self.initMT();
        }
      });
    },
    getFileContent: function (file) {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/files/get_file_content",
        data: { file: file },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.showFile = Object.assign({}, file, {
              file_content: response.file_content,
              isImagen: false,
            });
            self.$nextTick(function () {
              if (window.Prism) {
                Prism.highlightAll();
              }
            });
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    saveFileContent: function () {
      var self = this;
      if (!this.showFile.file_id) {
        return;
      }
      this.savingContent = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/save_content",
        data: {
          file_id: this.showFile.file_id,
          content: this.showFile.file_content,
        },
        dataType: "json",
        success: function (response) {
          self.savingContent = false;
          if (response.code == 200) {
            self.toast("files_saved");
          } else {
            self.toastError(null, response);
          }
        },
        error: function (xhr) {
          self.savingContent = false;
          self.toastError(xhr);
        },
      });
    },
    pickReplaceFile: function (file) {
      this.replaceTarget = file;
      if (this.$refs.replaceInput) {
        this.$refs.replaceInput.click();
      }
    },
    replaceFileServe: function (event) {
      var self = this;
      var input = event.target;
      if (!input.files || !input.files[0] || !this.replaceTarget) {
        return;
      }
      var data = new FormData();
      data.append("file", input.files[0]);
      data.append("file_id", this.replaceTarget.file_id);
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/files/ajax_replace_file",
        data: data,
        processData: false,
        contentType: false,
        success: function (response) {
          if (response && response.ok) {
            self.toast("files_replace_ok");
            self.refreshCurrent();
          } else {
            self.toast("toast_error");
          }
          input.value = "";
        },
        error: function (xhr) {
          self.toastError(xhr);
          input.value = "";
        },
      });
    },
    startNewFolder: function () {
      this.creatingFolder = true;
      this.newFolderName = this.t("files_new_folder");
      var self = this;
      this.$nextTick(function () {
        if (self.$refs.folderNameInput) {
          self.$refs.folderNameInput.focus();
          self.$refs.folderNameInput.select();
        }
      });
    },
    makeFolderServer: function () {
      var self = this;
      var name = (this.newFolderName || "").trim();
      this.creatingFolder = false;
      if (!name) {
        return;
      }
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/files/make_dir",
        data: { path: self.curDir, new_folder_name: name },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("files_folder_created");
            self.navigateFiles(self.curDir);
          } else {
            self.toastError(null, response);
          }
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    trashSelected: function () {
      this.moveToTrash = { bulk: true };
    },
    confirmTrash: function () {
      var dest = this.libraryTrashPath();
      var self = this;
      if (this.moveToTrash && this.moveToTrash.bulk) {
        this.selectedFileObjects().forEach(function (file) {
          self.moveFileTo(file, dest);
        });
        return;
      }
      this.moveFileTo(this.moveToTrash, dest);
    },
    confirmDelete: function () {
      var self = this;
      if (this.moveToTrash && this.moveToTrash.bulk) {
        this.selectedFileObjects().forEach(function (file) {
          self.deleteFile(file);
        });
        return;
      }
      this.deleteFile(this.moveToTrash);
    },
    sidebarActive: function (name) {
      if (name === "themes") {
        return this.inThemes ? "current" : "";
      }
      if (name === null) {
        return !this.activeFilter && !this.inThemes ? "current" : "";
      }
      return this.activeFilter === name ? "current" : "";
    },
    initMT: function () {
      var self = this;
      this.$nextTick(function () {
        self.reinitPlugin(".dropdown-trigger", M.Dropdown, {
          constrainWidth: false,
        });
        self.reinitPlugin(".modal", M.Modal, {});
        var tabs = document.getElementById("filetabs");
        if (tabs) {
          M.Tabs.init(tabs, {});
        }
      });
    },
  },
  filters: {
    shortName: function (value) {
      if (!value) return "";
      value = value.toString();
      if (value.length > 18) {
        return value.substr(0, 18) + "...";
      }
      return value;
    },
  },
  mounted: function () {
    var self = this;
    this.replaceTarget = null;
    this.$nextTick(function () {
      var params = Object.fromEntries(
        new URLSearchParams(window.location.search).entries()
      );
      if (params.path) {
        self.navigateFiles(params.path);
      } else {
        self.navigateFiles(self.root);
      }
      self.loadRecentsStrip();
    });
  },
});
