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
  mixins: [mixins],
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
          self.content.map((element) => {
            element.user = new User(element.user);
            element.status = element.status == "1";
            return element;
          });
        },
      });

      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        var instances = M.Collapsible.init(elems, {});
      }, 3000);
    },
    isActive(item) {
      return item.status;
    },
    toggleStatus(item) {
      var self = this;
      $.ajax({
        type: "POST",
        url: BASEURL + "api/v1/forms/data_set_status/" + item.form_content_id,
        data: {
          status: item.status ? "1" : "0"
        },
        dataType: "json",
        success: function (response) {
          console.log(response);
          M.toast({ html: "Actualizado" });
        },
      });
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
