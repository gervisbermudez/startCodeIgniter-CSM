var EventsList = new Vue({
  el: "#root",
  data: {
    colums: [
      {
        colum: "name",
        label: "name",
      },
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "date_create",
        label: "Creado",
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
    editItem(data) {
      window.location = `${BASEURL}admin/siteforms/editar/${data.item.siteform_id}`;
      return;
    },
    deleteItem(data) {
      console.log({ data });
      return;
    },
    archiveItem(data) {
      console.log({ data });
      return;
      },
      newEvent() {
        window.location = `${BASEURL}admin/eventos/agregar/`;
        return;
      }
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
