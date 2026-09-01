Vue.component("createContents", {
  template: "#create-contents-template",
  props: ["forms_types", "content", "total"],
  data: function () {
    return {
      debug: DEBUGMODE,
      i18n: window.COLLECTIONS_I18N || {},
    };
  },
  mixins: [mixins],
  methods: {
    toggleStatus: function (item, e) {
      var prev = item.status;
      var status = e.target.checked ? 1 : 2;
      item.status = status;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/models/data_set_status/" + item.custom_model_content_id,
        data: { status: status },
        dataType: "json",
        error: function () {
          item.status = prev;
          e.target.checked = prev == 1 || prev == "1";
          M.toast({ html: (window.COLLECTIONS_I18N && window.COLLECTIONS_I18N.error) || "" });
        },
      });
    },
    getFormsTypeUrl: function (formObject) {
      return BASEURL + "admin/custommodels/addData/" + formObject.custom_model_id;
    },
    base_url: function (path) {
      return BASEURL + path;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: createContents") : null;
    });
  },
});
