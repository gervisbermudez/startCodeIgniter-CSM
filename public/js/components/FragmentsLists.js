var FragmentsLists = new Vue({
  el: "#root",
  data: {
    fragments: [],
    tableView: false,
    loader: true,
    filter: "",
    toDeleteItem: {},
  },
  mixins: [mixins],
  computed: {
    filterFragments: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.fragments.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.fragments;
      }
    },
  },
  methods: {
    getcontentText: function (fragment) {
      return fragment.description.substring(0, 50) + "...";
    },
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    resetFilter: function () {
      this.filter = "";
    },
    getPageImagePath(fragment) {
      if (fragment.imagen_file) {
        return (
          BASEURL +
          fragment.imagen_file.file_path.substr(2) +
          fragment.imagen_file.file_name +
          "." +
          fragment.imagen_file.file_type
        );
      }
      return BASEURL + "/public/img/default.jpg";
    },
    getFragments: function () {
      var self = this;
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/fragments/",
        data: {},
        dataType: "json",
        success: function (response) {
          let fragments = response.data;
          for (const key in fragments) {
            if (fragments.hasOwnProperty(key)) {
              fragments[key].user = new User(fragments[key].user);
            }
          }
          self.fragments = fragments;
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
          console.error(error);
        },
      });
    },
    delete: function (fragment, index) {
      var self = this;
      self.loader = true;
      $.ajax({
        type: "DELETE",
        url: BASEURL + "api/v1/fragments/" + fragment.fragment_id,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.code == 200) {
            self.fragments.splice(index, 1);
          }
          setTimeout(() => {
            self.loader = false;
            self.initPlugins();
          }, 1000);
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: "An unexpected error occurred" });
          console.error(error);
        },
      });
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
    },
    confirmCallback(data) {
      if (data) {
        this.delete(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
    base_url: function (path) {
      return BASEURL + path;
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        if (elems.length > 0) {
          M.Tooltip.init(elems, {});
        }
        var elems = document.querySelectorAll(".dropdown-trigger");
        if (elems.length > 0) {
          M.Dropdown.init(elems, {});
        }
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      this.getFragments();
    });
  },
});
