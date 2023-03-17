var SiteFormSubmitList = new Vue({
  el: "#root",
  router: new VueRouter({
    routes: [
      {
        name: "table",
        path: "/",
        component: dataTable,
        props: true,
      },
    ],
  }),
  data: {
    endpoint: "api/v1/siteforms/submit/",
    colums: [
      {
        colum: "user_tracking_id",
        label: "tracking_id",
      },
      {
        colum: "SiteForm.name",
        label: "name",
      },
      {
        colum: "SiteForm.template",
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
    newItem() {
      window.location = `${BASEURL}admin/siteforms/nuevo/`;
      return;
    },
    editItem(data) {
      window.location = `${BASEURL}admin/siteforms/editar/${data.item.siteform_id}`;
      return;
    },
    deleteItem({ item }) {
      var self = this;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/siteforms/" + item.siteform_id,
        data: {},
        dataType: "json",
        success: function (response) {
          window.location.reload();
        },
        error: function (error) {
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
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
