var FormSiteDetails = Vue.component("FormSiteDetails", {
  template: "#FormSiteDetails-template",
  props: {},
  data: function () {
    return {
      loader: true,
      endpoint: "api/v1/siteforms/submit/",
      data: {
        siteform_submit_data: {},
        date_create: "",
        siteform: {
          date_create: "",
        },
        user_tracking: {
          client_ip: "",
          date_create: "",
          no_of_visits: "",
          user_agent: "",
        },
      },
      user: {},
    };
  },
  mixins: [mixins],
  filters: {
    capitalize: function (value) {
      if (!value) return "";
      value = value.toString();
      return value.charAt(0).toUpperCase() + value.slice(1);
    },
  },
  computed: {
    keys: function () {
      if (this.data.siteform_submit_data) {
        return Object.keys(this.data.siteform_submit_data);
      }
      return [];
    },
  },
  methods: {
    t: function (key, fallback) {
      if (window.SITEFORMS_I18N && window.SITEFORMS_I18N[key]) {
        return window.SITEFORMS_I18N[key];
      }
      return fallback || key;
    },
    back: function () {
      this.$router.push({
        name: "table",
      });
    },
    initPlugins: function () {
      var self = this;
      this.$nextTick(function () {
        M.Tabs.init(document.getElementById("formTabs"), {});
        M.Tooltip.init(document.querySelectorAll(".tooltipped"), {});
        M.Dropdown.init(document.querySelectorAll(".dropdown-trigger"), {});
        self.loader = false;
      });
    },
    getData: function () {
      var self = this;
      this.loader = true;
      var url = BASEURL + this.endpoint + this.$route.params.siteform_submit_id;
      fetch(url)
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          var data = response.data;
          self.data = data;
          if (data.siteform && data.siteform.user) {
            self.user = new User(data.siteform.user);
          } else {
            self.user = new User({});
          }
          self.loader = false;
          self.initPlugins();
        })
        .catch(function () {
          self.loader = false;
          M.toast({ html: self.t("error", "Error") });
        });
    },
    setArchive: function () {
      var self = this;
      var url =
        BASEURL +
        "api/v1/siteforms/submit_archive/" +
        this.$route.params.siteform_submit_id;
      fetch(url, {
        method: "POST",
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (response) {
          if (response.data) {
            self.data.status = 2;
            M.toast({ html: self.t("statusSeen", "Seen") });
          }
        })
        .catch(function (error) {
          self.loader = false;
          M.toast({ html: self.t("error", "Error") });
          console.error(error);
        });
    },
  },
  mounted: function () {
    var self = this;
    this.$nextTick(function () {
      if (self.$route.params.siteform_submit_id) {
        self.getData();
      } else {
        self.$router.push({
          name: "table",
        });
      }
    });
  },
});
