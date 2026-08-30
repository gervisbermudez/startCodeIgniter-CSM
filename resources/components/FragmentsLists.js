var FragmentsLists = new Vue({
  el: "#root",
  data: {
    fragments: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    serverPagination: true,
    listEndpoint: "api/v1/fragments",
    listKey: "fragments",
    listPk: "fragment_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterFragments: function () {
      if (this.serverPagination) {
        return this.fragments;
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.fragments.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.fragments;
    },
  },
  methods: {
    getFragments: function (page) {
      this.fetchList(page);
    },
    delete: function (fragment, index) {
      this.deleteListItem(fragment, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getFragments();
    });
  },
});
