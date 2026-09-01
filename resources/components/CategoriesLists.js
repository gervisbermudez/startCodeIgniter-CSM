var CategoriesLists = new Vue({
  el: "#root",
  data: {
    categories: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    currentStatus: null,
    serverPagination: true,
    listEndpoint: "api/v1/categories",
    listKey: "categories",
    listPk: "categorie_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterCategories: function () {
      if (this.serverPagination) {
        return this.categories;
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.categories.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.categories;
    },
  },
  methods: {
    getCategories: function (status, page) {
      if (typeof status === "undefined") {
        status = this.currentStatus;
      }
      if (status !== this.currentStatus) {
        page = 1;
      }
      this.currentStatus = status;
      this.fetchList(page);
    },
    delete: function (categorie, index) {
      this.deleteListItem(categorie, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getCategories();
    });
  },
});
