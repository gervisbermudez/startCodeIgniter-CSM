Vue.component("createContents", {
  template: "#create-contents-template",
  props: ["forms_types", "content"],
  data: function () {
    return {
      debug: DEBUGMODE,
    };
  },
  mixins: [mixins],
  methods: {
    isActive(item) {
      return item.status;
    },
    toggleStatus(item) {
      var self = this;
      $.ajax({
        type: "POST",
        url:
          BASEURL +
          "api/v1/models/data_set_status/" +
          item.custom_model_content_id,
        data: {
          status: item.status ? "1" : "0",
        },
        dataType: "json",
        success: function (response) {
          console.log(response);
          M.toast({ html: "Actualizado" });
        },
      });
    },
    getFormsTypeUrl(formObject) {
      return (
        BASEURL + "admin/CustomModels/addData/" + formObject.custom_model_id
      );
    },
    base_url(path) {
      return BASEURL + path;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: createContents") : null;
    });
  },
});
