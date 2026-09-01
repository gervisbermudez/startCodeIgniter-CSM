Vue.component("userCard", {
  template: "#user-card-template",
  props: ["user", "index"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  computed: {
    displayName: function () {
      return userDisplayName(this.user);
    },
    phone: function () {
      var data = this.user && this.user.user_data;
      if (!data || typeof data !== "object") {
        return "";
      }
      return this.hasValue(data.telefono) ? String(data.telefono).trim() : "";
    },
  },
  methods: {
    getUserUrl: function () {
      return BASEURL + "admin/users/ver/" + this.user.user_id;
    },
    requestDelete: function () {
      this.$emit("tempDelete", this.user, this.index);
    },
    hasValue: function (value) {
      if (value === null || value === undefined) {
        return false;
      }
      var text = String(value).trim();
      return text !== "" && text.toLowerCase() !== "undefined" && text.toLowerCase() !== "null";
    },
  },
});

function userDisplayName(user) {
  if (!user) {
    return "";
  }
  if (typeof user.get_fullname === "function") {
    var full = user.get_fullname();
    if (full) {
      return full;
    }
  }
  return user.username || "";
}

var usersModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    users: [],
    loader: true,
    tableView: false,
    filter: "",
    toDeleteItem: {},
    listEndpoint: "api/v1/users",
    listKey: "users",
    listPk: "user_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterUsers: function () {
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.users.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return this.users;
    },
  },
  methods: {
    wrapListItem: function (item) {
      return new User(item);
    },
    getUsers: function (page) {
      this.fetchList(page);
    },
    deleteItem: function (user, index) {
      this.deleteListItem(user, index);
    },
    userDisplayName: userDisplayName,
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsers();
    });
  },
});
