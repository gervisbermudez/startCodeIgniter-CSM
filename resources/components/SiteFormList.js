var SiteFormList = new Vue({
  el: "#root",
  data: function () {
    var i18n = window.SITEFORMS_I18N || {};
    return {
      endpoint: "api/v1/siteforms/",
      index_data: "siteform_id",
      fabTooltip: i18n.newTooltip || "",
      emptyTitle: i18n.empty || "",
      emptyCta: i18n.emptyCta || "",
      emptyHref: BASEURL + "admin/siteforms/nuevo/",
      confirmTitle: i18n.confirmDelete || "",
      confirmBody: i18n.confirmDeleteBody || "",
      colums: [
        {
          colum: "name",
          label: i18n.name || "Name",
        },
        {
          colum: "submissions_count",
          label: i18n.submissions || "Submissions",
        },
        {
          colum: "template",
          label: i18n.template || "Template",
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
            var active = parseInt(item.status, 10) === 1;
            return active ? labels.active || "Active" : labels.inactive || "Inactive";
          },
        },
        {
          colum: "options",
          label: i18n.options || "Options",
        },
      ],
      options: [
        {
          label: i18n.edit || "Edit",
          icon: "edit",
          url: "admin/siteforms/editar/",
          url_param: "siteform_id",
        },
        {
          label: i18n.viewSubmissions || "Submissions",
          icon: "inbox",
          url: "admin/siteforms/submit/?form=",
          url_param: "siteform_id",
        },
        {
          label: i18n.exportCsv || "Export",
          icon: "file_download",
          url: "admin/siteforms/export/",
          url_param: "siteform_id",
        },
        {
          label: i18n.copySnippet || "Copy",
          icon: "content_copy",
          handler: "copySnippet",
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
    newItem: function () {
      window.location = BASEURL + "admin/siteforms/nuevo/";
    },
    editItem: function (data) {
      window.location = BASEURL + "admin/siteforms/editar/" + data.item.siteform_id;
    },
    copySnippet: function (payload) {
      var name = payload && payload.item ? payload.item.name : "";
      var text = "{!! render_form('" + name + "') !!}";
      var message = (window.SITEFORMS_I18N && window.SITEFORMS_I18N.snippetCopied) || "Copied";
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function () {
          M.toast({ html: message });
        }).catch(function () {
          M.toast({ html: text });
        });
        return;
      }
      M.toast({ html: text });
    },
    deleteItem: function (payload) {
      var self = this;
      var item = payload && payload.item ? payload.item : payload;
      if (!item || !item.siteform_id) {
        return;
      }
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/siteforms/" + item.siteform_id,
        dataType: "json",
        success: function (response) {
          if (response && response.code == 200) {
            self.toast("toast_deleted");
            window.location.reload();
            return;
          }
          self.toastError(null, response);
        },
        error: function (xhr) {
          self.toastError(xhr);
        },
      });
    },
  },
  mounted: function () {
    this.$nextTick(function () {});
  },
});
