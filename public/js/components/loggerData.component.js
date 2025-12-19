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
    endpoint: "api/v1/config/systemlogger/",
    colums: [
      {
        colum: "user",
        label: "Author",
      },
      {
        colum: "comment",
        label: "Comment",
      },
      {
        colum: "type",
        label: "type",
      },
      {
        colum: "token",
        label: "token",
      },
      {
        colum: "type_description",
        label: "Description",
      },
      {
        colum: "type_link",
        label: "View",
          format: (item, colum) => {
              return `<a href="${item.type_link}">View</a>`;
        },
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
