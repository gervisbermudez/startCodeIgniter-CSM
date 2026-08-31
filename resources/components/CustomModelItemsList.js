var CustomModelItemsList = new Vue({
  el: "#root",
  data: {
    items: [],
    tableView: false,
    loader: true,
    filter: "",
    statusFilter: null,
    serverPagination: true,
    custom_model_id: window.COLLECTION_ITEMS_MODEL_ID,
    i18n: window.COLLECTIONS_I18N || {},
    toDeleteItem: {},
  },
  mixins: [mixins],
  computed: {
    filterItems: function () {
      var list = this.items;
      if (this.statusFilter !== null) {
        list = list.filter(function (item) {
          return String(item.status) === String(this.statusFilter);
        }.bind(this));
      }
      if (this.filter) {
        var term = this.filter.toLowerCase();
        list = list.filter(function (value) {
          return this.searchInObject(value, term);
        }.bind(this));
      }
      return list;
    },
  },
  methods: {
    setStatusFilter: function (status) {
      this.statusFilter = status;
    },
    getItemImagePath: function (item) {
      var data = item.data || {};
      var keys = Object.keys(data);
      for (var i = 0; i < keys.length; i++) {
        var val = data[keys[i]];
        if (val && typeof val === "object" && val.url) {
          return val.url;
        }
      }
      return BASEURL + "public/img/default.jpg";
    },
    getItems: function (page) {
      var self = this;
      self.loader = true;
      var extra = { custom_model_id: this.custom_model_id };
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/models/data",
        data: this.listQuery(extra, page),
        dataType: "json",
        success: function (response) {
          self.items = (response.data || []).map(function (element) {
            if (element.user) {
              element.user = new User(element.user);
            }
            return element;
          });
          self.applyPaginatorFromResponse(response);
          self.loader = false;
          self.$nextTick(function () {
            self.initPlugins();
          });
        },
        error: function () {
          self.loader = false;
          M.toast({ html: self.i18n.unexpected || "" });
        },
      });
    },
    reloadList: function (page) {
      this.getItems(page);
    },
    deleteItem: function (item) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/models/data/" + item.custom_model_content_id,
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.getItems();
          } else {
            self.loader = false;
          }
        },
        error: function () {
          self.loader = false;
          M.toast({ html: self.i18n.unexpected || "" });
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback: function (ok) {
      if (ok) {
        this.deleteItem(this.toDeleteItem.item);
      }
    },
    initPlugins: function () {
      var elems = document.querySelectorAll(".tooltipped");
      M.Tooltip.init(elems, {});
      elems = document.querySelectorAll(".dropdown-trigger");
      M.Dropdown.init(elems, {});
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getItems();
    });
  },
});
