Vue.component("createContents", {
  template: "#create-contents-template",
  props: [],
  data: function () {
    return {
      debug: false,
      loader: false,
      forms_types: [],
      content: [],
    };
  },
  methods: {
    getDataFromServe() {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/forms/",
        data: {},
        dataType: "json",
        success: function (response) {
          console.log(response);
          self.forms_types = response.data;
        },
      });

      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/forms/data",
        data: {},
        dataType: "json",
        success: function (response) {
          console.log(response);
          self.content = response.data;
        },
      });

      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        var instances = M.Collapsible.init(elems, {});
      }, 3000);
    },
    getFormsTypeUrl(formObject) {
      return BASEURL + "admin/formularios/addData/" + formObject.form_custom_id;
    },
    base_url(path) {
      return BASEURL + path;
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.debug ? console.log("mounted: createContents") : null;
      this.getDataFromServe();
    });
  },
});
