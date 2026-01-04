var CustomModelsLists = new Vue({
  el: "#root",
  data: {
    models: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    modalid: 'deleteModal'
  },
  mixins: [mixins],
  computed: {
    filterModels: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.models.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.models;
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
    getPageImagePath(model) {
      if (model.imagen_file) {
        return (
          BASEURL +
          model.imagen_file.file_path.substr(2) +
          model.imagen_file.file_name +
          "." +
          model.imagen_file.file_type
        );
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getModels: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/models/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.models = response.data.map((element) => {
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
    deleteForm: function (model, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/models/" + model.custom_model_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.models.splice(index, 1);
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
    tempDelete: function (model, index) {
      this.toDeleteItem.item = model;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.deleteForm(this.toDeleteItem.item, this.toDeleteItem.index);
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
      this.getModels();
      this.init();
    });
  },
});
