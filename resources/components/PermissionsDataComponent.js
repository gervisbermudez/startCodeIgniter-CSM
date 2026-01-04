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
    endpoint: "api/v1/users/allpermissions/",
    colums: [
      {
        colum: "permision_name",
        label: "Name",
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
    index_data: "usergroup_id",
  },
  mixins: [mixins],
  computed: {},
  methods: {
    editItem(data) {
      console.log({ data });
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
