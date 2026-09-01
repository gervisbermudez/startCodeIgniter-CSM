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
    collectionItemCount: window.COLLECTION_ITEMS_COUNT || 0,
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
      if (this.filter && !this.serverPagination) {
        var term = this.filter.toLowerCase();
        list = list.filter(function (value) {
          return this.searchInObject(value, term);
        }.bind(this));
      }
      return list;
    },
    isCollectionEmpty: function () {
      return this.collectionItemCount === 0 && !this.filter && this.statusFilter === null;
    },
    isStatusFilterEmpty: function () {
      return (
        !this.filter &&
        this.statusFilter !== null &&
        this.filterItems.length === 0
      );
    },
    statusEmptyMessage: function () {
      if (this.statusFilter === 2) {
        return this.i18n.noDrafts || "";
      }
      if (this.statusFilter === 1) {
        return this.i18n.noPublished || "";
      }
      return this.i18n.noFilterResults || "";
    },
  },
  methods: {
    setStatusFilter: function (status) {
      this.statusFilter = status;
    },
    clearListFilters: function () {
      this.filter = "";
      this.statusFilter = null;
      this.getItems(1);
    },
    itemTitle: function (item) {
      if (item.title && String(item.title).indexOf("Collection #") !== 0) {
        return item.title;
      }
      var data = item.data || {};
      if (data.title && typeof data.title !== "object") {
        return data.title;
      }
      var keys = Object.keys(data);
      for (var i = 0; i < keys.length; i++) {
        var val = data[keys[i]];
        if (val && typeof val !== "object") {
          return val;
        }
      }
      return item.title || (this.i18n.fallbackTitle || "Collection") + " #" + item.custom_model_content_id;
    },
    getItemImagePath: function (item) {
      var data = item.data || {};
      var prefer = ["image", "imagen", "photo", "picture"];
      var i;
      for (i = 0; i < prefer.length; i++) {
        if (data[prefer[i]] && data[prefer[i]].url) {
          return data[prefer[i]].url;
        }
      }
      var keys = Object.keys(data);
      for (i = 0; i < keys.length; i++) {
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
          if (!self.filter) {
            self.collectionItemCount = response.total_rows != null ? Number(response.total_rows) : self.items.length;
          }
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
    resetFilter: function () {
      this.filter = "";
      this.getItems(1);
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
