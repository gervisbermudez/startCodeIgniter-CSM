var CustomModelsLists = new Vue({
  el: "#root",
  data: {
    models: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
    modalid: "deleteModal",
    i18n: window.COLLECTIONS_I18N || {},
  },
  mixins: [mixins],
  computed: {
    filterModels: function () {
      if (!!this.filter) {
        var filterTerm = this.filter.toLowerCase();
        return this.models.filter(function (value) {
          return this.searchInObject(value, filterTerm);
        }.bind(this));
      }
      return this.models;
    },
  },
  methods: {
    copySnippet: function (model) {
      var text = model.snippet || "{!! get_collection('" + model.slug + "') !!}";
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text);
      } else {
        var input = document.createElement("input");
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
      }
      M.toast({ html: this.i18n.copied || "" });
    },
    getModels: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/models/",
        data: {},
        dataType: "json",
        success: function (response) {
          self.models = (response.data || []).map(function (element) {
            if (element.user) {
              element.user = new User(element.user);
            }
            return element;
          });
          self.loader = false;
          self.$nextTick(function () {
            self.initPlugins();
          });
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: self.i18n.unexpected || "" });
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
          self.loader = false;
          self.$nextTick(function () {
            self.initPlugins();
          });
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: self.i18n.unexpected || "" });
          console.error(error);
        },
      });
    },
    tempDelete: function (model, index) {
      this.toDeleteItem.item = model;
      this.toDeleteItem.index = index;
    },
    confirmCallback: function (data) {
      if (data) {
        this.deleteForm(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
    initPlugins: function () {
      var elems = document.querySelectorAll(".tooltipped");
      M.Tooltip.init(elems, {});
      elems = document.querySelectorAll(".dropdown-trigger");
      M.Dropdown.init(elems, {});
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getModels();
    });
  },
});
