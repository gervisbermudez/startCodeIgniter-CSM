var UserGroupsComponents = new Vue({
  el: "#root",
  mixins: [mixins, listMixin],
  data: {
    listEndpoint: "api/v1/users/usergroups",
    listKey: "usergroups",
    listPk: "usergroup_id",
    usergroups: [],
    tableView: true,
    loader: true,
    filter: "",
    currentStatus: null,
  },
  computed: {
    filterUsergroups: function () {
      var list = this.usergroups;
      if (this.currentStatus === 1) {
        list = list.filter(function (value) {
          return parseInt(value.status, 10) === 1;
        });
      } else if (this.currentStatus === 2) {
        list = list.filter(function (value) {
          return parseInt(value.status, 10) === 2;
        });
      }
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        var self = this;
        return list.filter(function (value) {
          return self.searchInObject(value, filterTerm);
        });
      }
      return list;
    },
  },
  methods: {
    setStatus: function (status) {
      this.currentStatus = status;
    },
    getUserGroups: function () {
      var self = this;
      self.loader = true;
      fetch(BASEURL + "api/v1/users/usergroups/")
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          var usergroups = response && response.code == 200 && response.data ? response.data : [];
          if (!Array.isArray(usergroups)) {
            usergroups = Object.keys(usergroups).map(function (k) {
              return usergroups[k];
            });
          }
          self.usergroups = usergroups.map(function (element) {
            if (element.user) {
              element.user = new User(element.user);
            } else {
              element.user = new User({});
            }
            return element;
          });
          self.loader = false;
          self.initPlugins();
        })
        .catch(function (error) {
          self.loader = false;
          self.usergroups = [];
          self.toast("usergroups_unexpected_error");
          console.error(error);
        });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUserGroups();
    });
  },
});
