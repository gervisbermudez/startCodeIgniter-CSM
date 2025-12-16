var UserTrackingLoggerData = new Vue({
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
    endpoint: "api/v1/config/usertrackinglogger/",
    colums: [
      {
        colum: "requested_url",
        label: "URI",
      },
      {
        colum: "referer_page",
        label: "referer_page",
      },
      {
        colum: "page_name",
        label: "page_name",
      },
      {
        colum: "client_ip",
        label: "client_ip",
      },
      {
        colum: "query_string",
        label: "query_string",
      },
      {
        colum: "date_create",
        label: "Date",
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
