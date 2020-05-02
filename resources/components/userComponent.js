Vue.component("userCard", {
  template: "#user-card-template",
  props: ["user"],
  data: function () {
    return {};
  },
  methods: {
    getAvatarUrl() {
      return (
        BASEURL +
        "public/img/profile/" +
        this.user.username +
        "/" +
        this.user.user_data.avatar
      );
    },
    getUserUrl() {
      return BASEURL + "admin/usuarios/ver/" + this.user.user_id;
    },
    base_url: function (path) {
      return BASEURL + path;
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});

var usersModule = new Vue({
  el: "#root",
  data: {
    users: [],
    loader: true,
    tableView : false,
    filter : "",
  },
  computed: {
    filterUsers: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.users.filter((value, index) => {
          let result = value.username.toLowerCase().indexOf(filterTerm) != -1 || value.email.toLowerCase().indexOf(filterTerm) != -1 || value.role.toLowerCase().indexOf(filterTerm) != -1 || value.user_data.nombre.toLowerCase().indexOf(filterTerm) != -1 || value.user_data.apellido.toLowerCase().indexOf(filterTerm) != -1;
          return result;
        });
      } else {
        return this.users;
      }
    }
  },
  watch: {
    filter: function (value) {
      this.initPlugins();
    }
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
          self.users = response.data;
        },
        error: function (error) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
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
      this.getUsers();
    });
  },
});
