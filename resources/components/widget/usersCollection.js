Vue.component("usersCollection", {
  template: "#user-collection-template",
  data: function () {
    return {
      debug: DEBUGMODE,
      users: [],
    };
  },
  methods: {
    getUsers() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/",
        data: {},
        dataType: "json",
        success: function (response) {
          this.debug ? console.log(response) : null;
          let users = response.data;
          self.users = users.map((user) => new User(user));
        },
        error: function (error) {
          console.error(error);
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getUsers();
      this.debug ? console.log("mounted: usersCollection") : null;
    });
  },
});
