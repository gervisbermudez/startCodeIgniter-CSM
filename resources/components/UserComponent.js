Vue.component("userCard", {
  template: "#user-card-template",
  props: ["user", "index"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    getUserUrl: function () {
      return BASEURL + "admin/users/ver/" + this.user.user_id;
    },
    requestDelete: function () {
      this.$emit("tempDelete", this.user, this.index);
    },
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
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsers();
    });
  },
});
