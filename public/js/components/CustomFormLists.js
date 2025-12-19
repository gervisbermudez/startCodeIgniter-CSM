var CustomFormLists = new Vue({
  el: "#root",
  data: {
    forms: [],
    tableView: false,
    loader: true,
    filter: "",
  },
  mixins: [mixins],
  computed: {
    filterForms: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.forms.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.forms;
      }
    },
  },
  methods: {
    toggleView: function () {
      this.tableView = !this.tableView;
      this.init();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(form) {
      if (form.imagen_file) {
        return (
          BASEURL +
          form.imagen_file.file_path.substr(2) +
          form.imagen_file.file_name +
          "." +
          form.imagen_file.file_type
        );
      }
      return BASEURL + "public/img/default.jpg";
    },
    getForms: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/models/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.forms = response.data.map((element) => {
            element.user = new User(element.user);
            return element;
          });
          setTimeout(() => {
            self.loader = false;
            self.init();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    deleteForm: function (form, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/models/" + form.custom_model_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.forms.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.init();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    tempDelete: function (form, index) {
      this.toDeleteItem.form = form;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.deleteForm(this.toDeleteItem.form, this.toDeleteItem.index);
      }
    },
    init: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getForms();
      this.init();
    });
  },
});
