var PermissionsData = new Vue({
  el: "#root",
  router: new VueRouter({
    routes: [
      {
        name: "table",
        path: "/",
        component: dataTable,
        props: true,
      },
      {
        name: "edit",
        path: "/edit",
        component: dataEdit,
        props: {
          data: [],
        },
      },
    ],
  }),
  data: {
    endpoint: "api/v1/siteforms/",
    colums: [
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "name",
        label: "name",
      },
      {
        colum: "template",
        label: "template",
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
    index_data: "logger_id",
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
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});