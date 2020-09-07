var loginForm = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    username: "",
    password: "",
    redirect: "",
    remember_user: false,
    userdata: null,
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
        url: BASEURL + "/api/v1/login/",
        data: {
          username: this.username,
          password: this.password,
        },
        dataType: "json",
        success: function (response) {
          if (self.remember_user) {
            localStorage.removeItem("userdata");
            localStorage.setItem(
              "userdata",
              JSON.stringify(response.userdata[0])
            );
          }
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
    resetUserdata() {
      this.userdata = null;
      this.username = "";
      this.password = "";
      localStorage.removeItem("userdata");
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
    getRememberUserdata() {
      let userdata = JSON.parse(localStorage.getItem("userdata"));
      if (userdata) {
        console.log(userdata);
        this.userdata = userdata;
        this.username = userdata.username;
        this.remember_user = !!userdata;
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("mounted loginForm");
      this.loader = false;
      this.getRememberUserdata();
      let redirectpath = this.getUrlParameter("redirect");
      this.redirect = redirectpath == "admin" ? "" : redirectpath;
    });
  },
});
