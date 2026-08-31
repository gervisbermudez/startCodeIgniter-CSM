var FormSiteDetails = Vue.component("FormSiteDetails", {
  template: "#FormSiteDetails-template",
  props: {},
  data: function () {
    return {
      loader: true,
      archiving: false,
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
    submitId: function () {
      return this.$route.params.siteform_submit_id || "";
    },
    isNew: function () {
      return parseInt(this.data.status, 10) === 1;
    },
    statusLabel: function () {
      return this.isNew
        ? this.t("statusNew", "New")
        : this.t("statusSeen", "Seen");
    },
    formName: function () {
      return (this.data.siteform && this.data.siteform.name) || "";
    },
    formIsActive: function () {
      return this.data.siteform && parseInt(this.data.siteform.status, 10) === 1;
    },
    tracking: function () {
      return this.data.user_tracking || {};
    },
  },
  methods: {
    t: function (key, fallback) {
      if (window.SITEFORMS_I18N && window.SITEFORMS_I18N[key]) {
        return window.SITEFORMS_I18N[key];
      }
      return fallback || key;
    },
    fieldLabel: function (key) {
      if (!key) {
        return "";
      }
      return String(key)
        .replace(/[_-]+/g, " ")
        .replace(/\b\w/g, function (char) {
          return char.toUpperCase();
        });
    },
    fieldValue: function (key) {
      var val = this.data.siteform_submit_data
        ? this.data.siteform_submit_data[key]
        : "";
      if (val === null || val === undefined || val === "") {
        return "";
      }
      if (typeof val === "object") {
        try {
          return JSON.stringify(val);
        } catch (e) {
          return "";
        }
      }
      return String(val);
    },
    isEmail: function (val) {
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
    },
    isLong: function (val) {
      return !!(val && (val.length > 80 || val.indexOf("\n") !== -1));
    },
    isLongField: function (key) {
      var val = this.fieldValue(key);
      var name = String(key || "").toLowerCase();
      if (
        name === "message" ||
        name === "comment" ||
        name === "body" ||
        name.indexOf("message") !== -1
      ) {
        return !!val;
      }
      return this.isLong(val);
    },
    copyText: function (text) {
      var self = this;
      var done = function () {
        M.toast({ html: self.t("copied", "Copied") });
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(function () {
          self.copyTextFallback(text, done);
        });
        return;
      }
      this.copyTextFallback(text, done);
    },
    copyTextFallback: function (text, done) {
      var area = document.createElement("textarea");
      area.value = text;
      area.setAttribute("readonly", "");
      area.style.position = "absolute";
      area.style.left = "-9999px";
      document.body.appendChild(area);
      area.select();
      try {
        document.execCommand("copy");
        done();
      } catch (e) {
        M.toast({ html: this.t("error", "Error") });
      }
      document.body.removeChild(area);
    },
    back: function () {
      this.$router.push({
        name: "table",
      });
    },
    initPlugins: function () {
      var self = this;
      this.$nextTick(function () {
        var tabsEl = document.getElementById("formTabs");
        if (tabsEl) {
          M.Tabs.init(tabsEl, {});
        }
        M.Tooltip.init(document.querySelectorAll(".form-site-details .tooltipped"), {});
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
      if (this.archiving || !this.isNew) {
        return;
      }
      this.archiving = true;
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
          self.archiving = false;
          if (response.data) {
            self.data.status = 2;
            M.toast({ html: self.t("statusSeen", "Seen") });
          }
        })
        .catch(function (error) {
          self.archiving = false;
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
