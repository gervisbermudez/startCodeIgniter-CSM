var ApiloggerData = new Vue({
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
    endpoint: "api/v1/config/apilogger/",
    colums: [
      {
        colum: "uri",
        label: "URI",
      },
      {
        colum: "method",
        label: "Method",
      },
      {
        colum: "ip_address",
        label: "ip_address",
      },
      {
        colum: "date_create",
        label: "Date",
      },
      {
        colum: "authorized",
        label: "Authorized",
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
    index_data: "api_log_id",
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
