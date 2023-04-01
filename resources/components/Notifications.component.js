var Notifications = new Vue({
  el: "#notifications",
  data: {
    notifications: [],
  },
  mixins: [mixins],

  computed: {},
  methods: {
    getData: function () {
      this.loader = true;
      const url = BASEURL + "api/v1/dashboard/notifications";
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          this.notifications = response.data;
        })
        .catch((response) => {
          console.log(response);
        });
    },
    setArchive: function (notification, index) {
      const url =
        BASEURL +
        "api/v1/dashboard/notifications/" +
        notification.notification_id;
      fetch(url, {
        method: "POST",
      })
        .then((response) => response.json())
        .then((response) => {
          console.log(response);
          this.notifications.splice(index, 1);
        })
        .catch((error) => {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        });
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      console.log("Mounted");
      this.getData();
    });
  },
});
