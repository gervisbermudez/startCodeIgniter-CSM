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
    requestDelete: function (event) {
      if (event && event.preventDefault) {
        event.preventDefault();
      }
      this.$emit("temp-delete", this.user, this.index);
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
    currentStatus: null,
    listEndpoint: "api/v1/users",
    listKey: "users",
    listPk: "user_id",
  },
  mixins: [mixins, listMixin],
  computed: {
    filterUsers: function () {
      var list = this.users;
      if (this.currentStatus === 1) {
        list = list.filter(function (value) {
          return parseInt(value.status, 10) === 1;
        });
      } else if (this.currentStatus === 0) {
        list = list.filter(function (value) {
          return parseInt(value.status, 10) !== 1;
        });
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return list.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return list;
    },
  },
  methods: {
    wrapListItem: function (item) {
      return new User(item);
    },
    listExtraQuery: function () {
      return {};
    },
    setStatus: function (status) {
      this.currentStatus = status;
    },
    getUsers: function (page) {
      this.fetchList(page);
    },
    deleteItem: function (user, index) {
      this.deleteListItem(user, index);
    },
    userDisplayName: userDisplayName,
    onRequestDelete: function (user, index) {
      this.tempDelete(user, index);
      this.$nextTick(function () {
        var el = document.getElementById("deleteModal");
        if (!el || typeof M === "undefined" || !M.Modal) {
          return;
        }
        var inst = M.Modal.getInstance(el);
        if (!inst) {
          inst = M.Modal.init(el, {});
        }
        inst.open();
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsers();
    });
  },
});
