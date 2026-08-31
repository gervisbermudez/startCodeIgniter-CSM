var CustomModelsLists = new Vue({
  el: "#root",
  data: {
    models: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    modalid: "deleteModal",
    i18n: window.COLLECTIONS_I18N || {},
    serverPagination: true,
    listEndpoint: "api/v1/models",
    listKey: "models",
    listPk: "custom_model_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterModels: function () {
      if (this.serverPagination) {
        return this.models;
      }
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
    copySnippet: function (model) {
      var text = model.snippet || "{!! get_collection('" + model.slug + "') !!}";
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text);
      } else {
        var input = document.createElement("input");
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
      }
      M.toast({ html: this.i18n.copied || "" });
    },
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
