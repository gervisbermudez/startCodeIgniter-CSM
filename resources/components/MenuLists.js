var MenuLists = new Vue({
  el: "#root",
  data: {
    menus: [],
    tableView: true,
    loader: true,
    filter: "",
    toDeleteItem: {},
  },
  computed: {
    filterMenus: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.menus.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.menus;
      }
    },
  },
  methods: {
    getcontentText: function (menu) {
      return menu.description.substring(0, 50) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(menu) {
      return BASEURL + "public/img/default.jpg";
    },
    getMenus: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/menus/",
        data: {},
        dataType: "json",
        success: function (response) {
          let menus = response.data;
          for (const key in menus) {
            if (menus.hasOwnProperty(key)) {
              menus[key].user = new User(menus[key].user);
            }
          }
          self.menus = menus;
          self.loader = false;
          self.initPlugins();
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    deleteItem: function (menu, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/menus/" + menu.menu_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.menus.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    tempDelete: function (menu, index) {
      this.toDeleteItem.menu = menu;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.deleteItem(this.toDeleteItem.menu, this.toDeleteItem.index);
      }
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getMenus();
    });
  },
});
