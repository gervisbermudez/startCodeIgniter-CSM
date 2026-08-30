var NotesLists = new Vue({
  el: "#root",
  data: {
    notes: [],
    tableView: false,
    loader: true,
    filter: "",
    serverPagination: true,
    listEndpoint: "api/v1/notes",
    listKey: "notes",
    listPk: "note_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterNotes: function () {
      if (this.serverPagination) {
        return this.notes;
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.notes.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.notes;
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
    getNotes: function (page) {
      this.fetchList(page);
    },
    delete: function (note, index) {
      this.deleteListItem(note, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getNotes();
    });
  },
});
