var CustomModelsLists = new Vue({
  el: "#root",
  data: {
    models: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    modalid: "deleteModal",
    listEndpoint: "api/v1/models",
    listKey: "models",
    listPk: "custom_model_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterModels: function () {
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.models.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.models;
    },
  },
  methods: {
    getModels: function (page) {
      this.fetchList(page);
    },
    deleteForm: function (model, index) {
      this.deleteListItem(model, index);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getModels();
    });
  },
});
