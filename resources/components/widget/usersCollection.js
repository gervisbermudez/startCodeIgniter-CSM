Vue.component("usersCollection", {
  template: "#user-collection-template",
  props: ["users"],
  data: function () {
    return {
      debug: false,
    };
  },
  methods: {
    getUserAvatar(user) {
      if (user.user_data.avatar) {
        return (
          BASEURL +
          "public/img/profile/" +
          user.username +
          "/" +
          user.user_data.avatar
        );
      } else {
        return "https://materializecss.com/images/sample-1.jpg";
      }
    },
    getUserLink(user) {
      return BASEURL + "admin/usuarios/ver/" + user.user_id;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: usersCollection") : null;
    });
  },
});