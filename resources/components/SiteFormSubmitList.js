// Creación de una instancia Vue llamada SiteFormSubmitList
var SiteFormSubmitList = new Vue({
  // Elemento HTML donde se montará la instancia Vue
  el: "#root",

  // Configuración de las rutas para el enrutador VueRouter
  router: new VueRouter({
    routes: [
      {
        name: "table",
        path: "/",
        component: dataTable,
        props: true,
      },
      {
        name: "details",
        path: "/details/:siteform_submit_id",
        component: dataDeatils,
        props: {
          data: [],
        },
      },
    ],
  }),

  // Datos que se utilizarán en el componente "dataTable"
  data: {
    // Ruta de acceso a los datos de la tabla
    endpoint: "api/v1/siteforms/submit/",
    // Personalización de las columnas de la tabla
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
    // Nombre de la clave que contiene los datos en la respuesta
    index_data: "logger_id",
    options: [
      {
        label: "Detalles",
        icon: "fas fa-info-circle",
        href: "details",
        params: ["siteform_submit_id"],
      },
    ],
  },

  // Uso de mixins para extender la funcionalidad del componente
  mixins: [mixins],

  // Propiedades calculadas que se utilizan en el componente
  computed: {},

  // Métodos que se utilizan en el componente
  methods: {
    // Redirige al usuario a la página de creación de un nuevo item
    newItem() {
      window.location = `${BASEURL}admin/SiteForms/nuevo/`;
      return;
    },
    // Redirige al usuario a la página de edición de un item existente
    editItem(data) {
      window.location = `${BASEURL}admin/SiteForms/editar/${data.item.siteform_id}`;
      return;
    },
    // Elimina un item de la tabla
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
    // Archiva un item de la tabla
    archiveItem(data) {
      console.log({ data });
      return;
    },
  },

  // Se ejecuta cuando se ha montado la instancia Vue
  mounted: function () {
    this.$nextTick(function () {});
  },
});
