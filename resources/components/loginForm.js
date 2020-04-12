var loginForm = new Vue({
  el: "#root",
  data: {
    loader: true,
    username: "",
    password: "",
    redirect: "",
  },
  computed: {
    btnEnable: function () {
      return !!this.username && !!this.password ? true : false;
    },
  },
  methods: {
    login() {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "admin/login/ajax_verify_auth",
        data: {
          username: this.username,
          password: this.password,
        },
        dataType: "json",
        success: function (response) {
          window.location = self.redirect
            ? BASEURL + self.redirect
            : BASEURL + response.redirect;
        },
        error: function (response) {
          M.toast({ html: response.responseJSON.error_message });
          self.loader = false;
        },
      });
    },
    getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split("&"),
        sParameterName,
        i;
      for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] === sParam) {
          return sParameterName[1] === undefined
            ? true
            : decodeURIComponent(sParameterName[1]);
        }
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("mounted loginForm");
      this.loader = false;
      let redirectpath = this.getUrlParameter("redirect");
      this.redirect = redirectpath == "admin" ? "" : redirectpath;
    });
  },
});
