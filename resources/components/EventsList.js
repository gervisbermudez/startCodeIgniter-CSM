var EventsList = new Vue({
  el: "#root",
  data: {
    when: "all",
    tableEmpty: false,
    filterEmpty: false,
    colums: [
      {
        colum: "name",
        label: (window.EVENTS_I18N && EVENTS_I18N.name) || "Name",
      },
      {
        colum: "date_start",
        label: (window.EVENTS_I18N && EVENTS_I18N.start) || "Start",
        format: function (item) {
          return EventsList.formatStart(item);
        },
      },
      {
        colum: "address",
        label: (window.EVENTS_I18N && EVENTS_I18N.address) || "Address",
        format: function (item) {
          return EventsList.formatPlace(item);
        },
      },
      {
        colum: "status",
        label: (window.EVENTS_I18N && EVENTS_I18N.status) || "Status",
      },
      {
        colum: "options",
        label: (window.EVENTS_I18N && EVENTS_I18N.options) || "Options",
      },
    ],
    index_data: "event_id",
    canCreate: !!(window.EVENTS_PERMS && window.EVENTS_PERMS.create),
    canUpdate: !!(window.EVENTS_PERMS && window.EVENTS_PERMS.update),
    canDelete: !!(window.EVENTS_PERMS && window.EVENTS_PERMS.delete),
  },
  mixins: [mixins],
  computed: {
    endpoint: function () {
      return "api/v1/events?when=" + encodeURIComponent(this.when);
    },
  },
  methods: {
    formatStart: function (item) {
      if (!item || !item.date_start) {
        return "";
      }
      var allDay = item.all_day == 1 || item.all_day === true || item.all_day === "1";
      var value = String(item.date_start);
      if (allDay) {
        return value.substr(0, 10);
      }
      return value.substr(0, 16).replace("T", " ");
    },
    formatPlace: function (item) {
      if (!item) {
        return "";
      }
      var type = item.location_type || "physical";
      if (type === "online") {
        return (window.EVENTS_I18N && EVENTS_I18N.online) || "Online";
      }
      return item.address || "";
    },
    setWhen: function (when) {
      this.when = when;
      var self = this;
      this.$nextTick(function () {
        self.reloadEventsTable();
      });
    },
    editEvent(data) {
      window.location = `${BASEURL}admin/events/edit/${data.item.event_id}`;
      return;
    },
    reloadEventsTable() {
      if (this.$refs.eventsTable && typeof this.$refs.eventsTable.getData === "function") {
        this.$refs.eventsTable.getData(1);
      }
    },
    deleteItem(data) {
      var self = this;
      if (!data || !data.item || !data.item.event_id) {
        return;
      }
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/events/" + data.item.event_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("toast_done");
            self.reloadEventsTable();
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    archiveItem(data) {
      var self = this;
      if (!data || !data.item || !data.item.event_id) {
        return;
      }
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/events/status/" + data.item.event_id,
        data: {
          status: 3,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.toast("toast_done");
            self.reloadEventsTable();
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
    newEvent() {
      window.location = `${BASEURL}admin/events/add/`;
      return;
    },
    syncEmpty() {
      var table = this.$refs.eventsTable;
      if (!table) {
        this.tableEmpty = false;
        this.filterEmpty = false;
        return;
      }
      var noRows = !table.loader && !table.filter && table.data.length === 0;
      this.tableEmpty = noRows && this.when === "all";
      this.filterEmpty = noRows && this.when !== "all";
    },
  },
  mounted: function () {
    var self = this;
    this.$nextTick(function () {
      var table = self.$refs.eventsTable;
      if (!table) {
        return;
      }
      table.$watch("loader", function () {
        self.syncEmpty();
      });
      table.$watch("filter", function () {
        self.syncEmpty();
      });
      table.$watch(
        "data",
        function () {
          self.syncEmpty();
        },
        { deep: true }
      );
      self.$watch("when", function () {
        self.syncEmpty();
      });
      self.syncEmpty();
    });
  },
});
