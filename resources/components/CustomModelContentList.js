var CustomModelContentList = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    contents: [],
    tableView: false,
    loader: true,
    filter: "",
    currentStatus: null,
    serverPagination: true,
    listEndpoint: "api/v1/models/data",
    listKey: "contents",
    listPk: "custom_model_content_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterContents: function () {
      if (this.serverPagination) {
        return this.contents;
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.contents.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.contents;
    },
  },
  methods: {
    getcontentText: function (content) {
      if (!content || !content.data) {
        return "";
      }
      var text = Object.values(content.data).join(" ");
      return text.substring(0, 90) + "...";
    },
    getContents: function (status, page) {
      if (typeof status === "undefined") {
        status = this.currentStatus;
      }
      if (status !== this.currentStatus) {
        page = 1;
      }
      this.currentStatus = status;
      this.fetchList(page);
    },
    deleteContent: function (content, index) {
      this.deleteListItem(content, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getContents();
    });
  },
});
