var MenuLists = new Vue({
  el: "#root",
  data: {
    menus: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    currentStatus: null,
    listEndpoint: "api/v1/menus",
    listKey: "menus",
    listPk: "menu_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterMenus: function () {
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.menus.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.menus;
    },
  },
  methods: {
    getMenus: function (status, page) {
      if (typeof status === "undefined") {
        status = this.currentStatus;
      }
      if (status !== this.currentStatus) {
        page = 1;
      }
      this.currentStatus = status;
      this.fetchList(page);
    },
    deleteItem: function (menu, index) {
      this.deleteListItem(menu, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getMenus();
    });
  },
});
