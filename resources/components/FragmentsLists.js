var FragmentsLists = new Vue({
  el: "#root",
  data: {
    fragments: [],
    tableView: false,
    loader: true,
    filter: "",
    currentStatus: null,
    currentType: null,
    previewHtml: "",
    toDeleteItem: {},
    serverPagination: true,
    listEndpoint: "api/v1/fragments",
    listKey: "fragments",
    listPk: "fragment_id",
    fragment_types: [
      "contenido",
      "parrafo",
      "widget",
      "page",
      "formulario",
      "video",
      "foto",
      "evento",
    ],
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
    listExtraQuery: function () {
      var extra = {};
      if (this.currentStatus !== null) {
        extra.status = this.currentStatus;
      }
      if (this.currentType) {
        extra.type = this.currentType;
      }
      return extra;
    },
    getFragments: function (page) {
      this.fetchList(page);
    },
    setStatus: function (status) {
      this.currentStatus = status;
      this.fetchList(1);
    },
    setType: function (type) {
      this.currentType = type;
      this.fetchList(1);
    },
    clearListFilters: function () {
      this.filter = "";
      this.currentStatus = null;
      this.currentType = null;
      this.fetchList(1);
    },
    fragmentToken: function (fragment) {
      if (!fragment || !fragment.name) {
        return "";
      }
      return "{{" + "fragment(" + fragment.name + ")}}";
    },
    copyToken: function (fragment) {
      var text = this.fragmentToken(fragment);
      if (!text) {
        return;
      }
      var message = this.t("fragments_token_copied");
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          M.toast({ html: message });
        }).catch(function () {
          M.toast({ html: text });
        });
        return;
      }
      M.toast({ html: text });
    },
    openPreview: function (fragment) {
      this.previewHtml =
        fragment && fragment.description ? fragment.description : "";
      var el = document.getElementById("fragmentPreviewModal");
      if (!el || !window.M || !M.Modal) {
        return;
      }
      var inst = M.Modal.getInstance(el);
      if (!inst) {
        inst = M.Modal.init(el, {});
      }
      inst.open();
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
