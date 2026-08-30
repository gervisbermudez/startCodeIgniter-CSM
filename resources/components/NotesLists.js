var NotesLists = new Vue({
  el: "#root",
  data: {
    notes: [],
    tableView: false,
    loader: true,
    filter: "",
    serverPagination: true,
  },
  mixins: [mixins],
  computed: {
    filterNotes: function () {
      if (this.serverPagination) {
        return this.notes;
      }
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.notes.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.notes;
      }
    },
  },
  methods: {
    getcontentText: function (note) {
      var source = "";
      if (!note) {
        return "";
      }
      if (note.json_content != null && note.json_content !== "") {
        if (typeof note.json_content === "string") {
          source = note.json_content;
        } else if (typeof note.json_content === "object") {
          source =
            note.json_content.text ||
            note.json_content.content ||
            note.json_content.title ||
            "";
        }
      }
      if (!source && note.title) {
        source = note.title;
      }
      source = String(source || "");
      if (!source) {
        return "";
      }
      return source.length > 50 ? source.substring(0, 50) + "..." : source;
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    reloadList: function (page) {
      this.getNotes(page);
    },
    getPageImagePath(fragment) {
      if (fragment.imagen_file) {
        return (
          BASEURL +
          fragment.imagen_file.file_path.substr(2) +
          fragment.imagen_file.file_name +
          "." +
          fragment.imagen_file.file_type
        );
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getNotes: function (page) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/notes/",
        data: this.listQuery({}, page),
        dataType: "json",
        success: function (response) {
          let notes = response.data || [];
          for (const key in notes) {
            if (notes.hasOwnProperty(key) && notes[key].user) {
              notes[key].user = new User(notes[key].user);
            }
          }
          self.notes = notes;
          self.applyPaginatorFromResponse(response);
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    delete: function (note, index) {
      var self = this;
      if (!note || !note.note_id) {
        return;
      }
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/notes/" + note.note_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.getNotes();
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.delete(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getNotes();
      this.initPlugins();
    });
  },
});
