var Notifications = new Vue({
  el: "#notifications",
  mixins: [mixins],
  data: {
    notifications: [],
    pollTimer: null,
  },
  methods: {
    getData: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/notifications",
        dataType: "json",
        success: function (response) {
          var items =
            response && response.code == 200 && response.data
              ? response.data
              : [];
          self.notifications = Array.isArray(items) ? items : [];
          self.initDropdown();
        },
        error: function () {
          self.notifications = [];
        },
      });
    },
    initDropdown: function () {
      this.$nextTick(function () {
        var el = document.querySelector("#notifications .dropdown-trigger");
        if (!el || typeof M === "undefined" || !M.Dropdown) {
          return;
        }
        if (!M.Dropdown.getInstance(el)) {
          M.Dropdown.init(el, {
            constrainWidth: false,
            coverTrigger: false,
          });
        }
        var tips = document.querySelectorAll("#notifications .tooltipped");
        if (tips.length && M.Tooltip) {
          M.Tooltip.init(tips, {});
        }
      });
    },
    markRead: function (notification, index, navigate) {
      var self = this;
      $.ajax({
        type: "POST",
        url:
          BASEURL +
          "api/v1/notifications/read/" +
          notification.notification_id,
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            self.notifications = self.notifications.filter(function (item) {
              return item.notification_id !== notification.notification_id;
            });
            if (navigate && notification.url) {
              window.location = self.base_url(notification.url);
            }
          }
        },
        error: function () {
          M.toast({ html: lang("toast_error") });
        },
      });
    },
    openNotification: function (notification, index, event) {
      if (event) {
        event.preventDefault();
      }
      this.markRead(notification, index, true);
    },
    startPolling: function () {
      var self = this;
      this.stopPolling();
      this.pollTimer = setInterval(function () {
        if (!document.hidden) {
          self.getData();
        }
      }, 45000);
    },
    stopPolling: function () {
      if (this.pollTimer) {
        clearInterval(this.pollTimer);
        this.pollTimer = null;
      }
    },
    onVisibility: function () {
      if (!document.hidden) {
        this.getData();
      }
    },
  },
  mounted: function () {
    this.getData();
    this.initDropdown();
    this.startPolling();
    document.addEventListener("visibilitychange", this.onVisibility);
  },
  beforeDestroy: function () {
    this.stopPolling();
    document.removeEventListener("visibilitychange", this.onVisibility);
  },
});
