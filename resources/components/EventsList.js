var EventsList = new Vue({
  el: "#root",
  data: {
    colums: [
      {
        colum: "name",
        label: "Name",
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Created",
      },
      {
        colum: "status",
        label: "Status",
        handler: "publish",
      },
      {
        colum: "options",
        label: "Options",
      },
    ],
    index_data: "event_id",
    endpoint: "api/v1/events",
  },
  mixins: [mixins],
  computed: {},
  methods: {
    editEvent(data) {
      window.location = `${BASEURL}admin/events/edit/${data.item.event_id}`;
      return;
    },
    reloadEventsTable() {
      if (this.$refs.eventsTable && typeof this.$refs.eventsTable.getData === "function") {
        this.$refs.eventsTable.getData();
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
            M.toast({ html: "Done!" });
            self.reloadEventsTable();
            return;
          }
          M.toast({ html: response.error_message || "Ocurrió un error inesperado" });
        },
        error: function (xhr) {
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(xhr);
        },
      });
    },
    archiveItem(data) {
      var self = this;
      if (!data || !data.item || !data.item.event_id) {
        return;
      }
      var item = data.item;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/events",
        data: {
          event_id: item.event_id,
          name: item.name,
          subtitle: item.subtitle || "",
          content: item.content || item.name,
          address: item.address || "",
          visibility: item.visibility,
          mainImage: item.mainImage || "",
          categorie_id: item.categorie_id || 0,
          status: 3,
        },
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            M.toast({ html: "Done!" });
            self.reloadEventsTable();
            return;
          }
          M.toast({ html: response.error_message || "Ocurrió un error inesperado" });
        },
        error: function (xhr) {
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(xhr);
        },
      });
    },
    newEvent() {
      window.location = `${BASEURL}admin/events/add/`;
      return;
    },
  },
  mounted: function () {
    this.$nextTick(function () { });
  },
});
