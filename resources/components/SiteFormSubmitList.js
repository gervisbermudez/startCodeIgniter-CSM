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
      {
        name: "details",
        path: "/details/:siteform_submit_id",
        component: FormSiteDetails,
        props: true,
      },
    ],
  }),
  data: function () {
    var i18n = window.SITEFORMS_I18N || {};
    var formId = "";
    var match = window.location.search.match(/[?&]form=([^&]+)/);
    if (match) {
      formId = decodeURIComponent(match[1]);
    }
    return {
      endpoint: "api/v1/siteforms/submit/",
      index_data: "siteform_submit_id",
      emptyTitle: i18n.inboxEmpty || "",
      confirmTitle: i18n.confirmDelete || "",
      confirmBody: i18n.confirmDeleteBody || "",
      queryParams: formId ? { siteform_id: formId } : {},
      colums: [
        {
          colum: "siteform.name",
          label: i18n.form || "Form",
        },
        {
          colum: "preview",
          label: i18n.preview || "Preview",
        },
        {
          colum: "date_create",
          label: i18n.created || "Created",
        },
        {
          colum: "status",
          label: i18n.status || "Status",
          format: function (item) {
            var labels = window.SITEFORMS_I18N || {};
            return parseInt(item.status, 10) === 2
              ? labels.statusSeen || "Seen"
              : labels.statusNew || "New";
          },
        },
        {
          colum: "options",
          label: i18n.options || "Options",
        },
      ],
      options: [
        {
          label: i18n.details || "Details",
          icon: "info",
          href: "details",
          params: ["siteform_submit_id"],
        },
        {
          label: i18n.markSeen || "Seen",
          icon: "done",
          action: "archive",
        },
        {
          label: i18n.delete || "Delete",
          icon: "delete",
          action: "delete",
        },
      ],
    };
  },
  mixins: [mixins],
  methods: {
    deleteItem: function (payload) {
      var item = payload.item;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/siteforms/submit/" + item.siteform_submit_id,
        dataType: "json",
        success: function () {
          window.location.reload();
        },
        error: function () {
          M.toast({ html: (window.SITEFORMS_I18N && window.SITEFORMS_I18N.error) || "Error" });
        },
      });
    },
    archiveItem: function (payload) {
      var item = payload.item;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/siteforms/submit_archive/" + item.siteform_submit_id,
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            item.status = 2;
            M.toast({ html: (window.SITEFORMS_I18N && window.SITEFORMS_I18N.statusSeen) || "Seen" });
          }
        },
        error: function () {
          M.toast({ html: (window.SITEFORMS_I18N && window.SITEFORMS_I18N.error) || "Error" });
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
