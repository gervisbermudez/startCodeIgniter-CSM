var dashboardModule = new Vue({
  el: "#root",
  data: {
    loader: true,
    pages: [],
    users: [],
  },
  computed: {
    filterPages: function () {
      if (this.pages.length > 3) {
        return this.pages.slice(0, 3);
      }
      return this.pages;
    },
  },
  methods: {
    getPages: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/pages/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.pages = response.data;
        },
        error: function (error) {
          console.error(error);
        },
      });
    },
    getUsers() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/users/",
        data: {},
        dataType: "json",
        success: function (response) {
          console.log(response);
          self.users = response.data;
        },
        error: function (error) {
          console.error(error);
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getPages();
      this.getUsers();
      setTimeout(() => {
        M.AutoInit();
        this.loader = false;
      }, 3000);
    });
  },
});
