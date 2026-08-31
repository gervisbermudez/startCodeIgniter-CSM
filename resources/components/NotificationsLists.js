var NotificationsLists = new Vue({
  el: "#root",
  mixins: [mixins],
  data: {
    notifications: [],
    loader: true,
    filter: "",
    statusFilter: "all",
  },
  computed: {
    filterAll: function () {
      var list = this.notifications;
      if (this.filter) {
        var filterTerm = this.filter.toLowerCase();
        list = list.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }, this);
      }
      return list;
    },
  },
  methods: {
    getNotifications: function () {
      var self = this;
      this.loader = true;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/notifications",
        data: { status: this.statusFilter, limit: 100 },
        dataType: "json",
        success: function (response) {
          var items =
            response && response.code == 200 && response.data
              ? response.data
              : [];
          self.notifications = Array.isArray(items) ? items : [];
          self.loader = false;
          self.$nextTick(function () {
            if (typeof self.initPlugins === "function") {
              self.initPlugins();
            }
          });
        },
        error: function () {
          self.notifications = [];
          self.loader = false;
          M.toast({ html: lang("toast_error") });
        },
      });
    },
    setFilter: function (status) {
      this.statusFilter = status;
      this.getNotifications();
    },
    markRead: function (notification, index, navigate) {
      var self = this;
      if (!notification || notification.status == 2) {
        if (navigate && notification && notification.url) {
          window.location = this.base_url(notification.url);
        }
        return;
      }
      $.ajax({
        type: "POST",
        url:
          BASEURL +
          "api/v1/notifications/read/" +
          notification.notification_id,
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            notification.status = 2;
            if (self.statusFilter === "1") {
              self.notifications = self.notifications.filter(function (item) {
                return item.notification_id !== notification.notification_id;
              });
            }
            if (navigate && notification.url) {
              window.location = self.base_url(notification.url);
            } else {
              M.toast({ html: lang("notifications_marked") });
            }
          }
        },
        error: function () {
          M.toast({ html: lang("toast_error") });
        },
      });
    },
    openNotification: function (notification, index) {
      this.markRead(notification, index, true);
    },
    markAllRead: function () {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/notifications/read-all",
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            if (self.statusFilter === "1") {
              self.notifications = [];
            } else {
              self.notifications.forEach(function (item) {
                if (item.status == 1) {
                  item.status = 2;
                }
              });
            }
            M.toast({ html: lang("notifications_marked") });
          }
        },
        error: function () {
          M.toast({ html: lang("toast_error") });
        },
      });
    },
  },
  mounted: function () {
    this.getNotifications();
  },
});
