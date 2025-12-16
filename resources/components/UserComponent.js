Vue.component("userCard", {
  template: "#user-card-template",
  props: ["user"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    getUserUrl() {
      return BASEURL + "admin/users/ver/" + this.user.user_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});

var usersModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    users: [],
    loader: true,
    tableView: false,
    filter: "",
    toDeleteItem: {},
  },
  mixins: [mixins],
  computed: {
    filterUsers: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.users.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.users;
      }
    },
  },
  watch: {
    filter: function (value) {
      this.initPlugins();
    },
  },
  methods: {
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetSearch() {
      this.filter = "";
    },
    getUsers() {
      var self = this;
      self.loader = true;
      self.users = [];
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.loader = false;
          self.users = response.data.map((element) => {
            return new User(element);
          });
          self.initPlugins();
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
      }, 3000);
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    deleteItem: function (user, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/users/" + user.user_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.users.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    confirmCallback(data) {
      if (data) {
        this.deleteItem(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsers();
    });
  },
});
