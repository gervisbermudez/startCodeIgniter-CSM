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
  mixins: [mixins],
  computed: {
    btnEnable: function () {
      return !!this.username && !!this.password ? true : false;
    },
  },
  methods: {
    login() {
      var self = this;
      if (!this.btnEnable) {
        return;
      }
      this.loader = true;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/login/",
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
              JSON.stringify({ username: self.username })
            );
          }
          window.location = self.redirect
            ? BASEURL + self.redirect
            : BASEURL + response.redirect;
        },
        error: function (response) {
          self.loader = false;
          self.toast("toast_error");
          console.error(response);
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
    sanitizeRedirect(path) {
      if (!path || path === true || path === "admin") {
        return "";
      }
      try {
        path = decodeURIComponent(path);
      } catch (e) {
        return "";
      }
      if (
        path.indexOf("://") !== -1 ||
        path.indexOf("//") === 0 ||
        path.indexOf("\\") !== -1 ||
        !/^admin(\/[-a-zA-Z0-9_\/]*)?$/.test(path)
      ) {
        return "";
      }
      return path;
    },
    getRememberUserdata() {
      var stored = localStorage.getItem("userdata");
      if (!stored) {
        return;
      }
      try {
        var userdata = JSON.parse(stored);
      } catch (e) {
        localStorage.removeItem("userdata");
        return;
      }
      if (userdata && userdata.username) {
        this.username = userdata.username;
        this.userdata = null;
        this.remember_user = true;
      }
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("mounted loginForm");
      this.loader = false;
      this.getRememberUserdata();
      this.redirect = this.sanitizeRedirect(this.getUrlParameter("redirect"));
    });
  },
});
