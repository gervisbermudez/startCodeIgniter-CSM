var dataTable = Vue.component("dataTable", {
  template: "#dataTableComponent-template",
  props: {
    endpoint: {
      type: String,
      required: false,
    },
    index_data: {
      type: String,
      required: false,
    },
    hideids: {
      type: Boolean,
      default: false,
    },
    colums: {
      type: Array,
      required: false,
      default: null,
    },
    module: {
      type: String,
      required: false,
    },
  },
  data: function () {
    return {
      data: [],
      labels: [
        {
          colum: "name",
          label: "Name",
        },
      ],
      statusIcons: [
        {
          tooltip: "No permitido",
          icon: "not_interested",
        },
        {
          tooltip: "Permitido",
          icon: "publish",
        },
      ],
      tableView: false,
      loader: true,
      filter: "",
      orderDataConf: {
        strPropertyName: null,
        sort_as: "asc",
      },
    };
  },
  mixins: [mixins],
  computed: {
    filterData: function () {
      if (!!this.filter) {
        let filterTerm = this.filter.toLowerCase();
        return this.data.filter((value, index) => {
          return this.searchInObject(value, filterTerm);
        });
      } else {
        return this.data;
      }
    },
  },
  methods: {
    toggleView: function () {
      this.tableView = !this.tableView;
      this.initPlugins();
    },
    toggleEddit(configuration) {
      configuration.editable = !configuration.editable;
      this.$forceUpdate();
    },
    resetFilter: function () {
      this.filter = "";
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
      }, 3000);
    },
    getData: function () {
      var self = this;
      var url = BASEURL + this.endpoint;
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let data = response.data;
          data.map((element) => {
            if (element.user) {
              element.user = new User(element.user);
            } else {
              element.user = new User({});
            }
            return element;
          });
          if (data.length && !this.colums) {
            this.labels = Object.keys(data[0]);
            if (this.hideids) {
              this.labels = this.labels.filter((label) => {
                return label.indexOf("_id") == -1;
              });
            }
            this.colums = this.labels.map((label) => {
              return {
                colum: label,
                label: label,
              };
            });
          }
          self.data = data;
          self.loader = false;
          this.initPlugins();
        })
        .catch((response) => {
          self.loader = false;
        });
    },
    getContent(item, label) {
      let result = "";
      switch (label) {
        case "user":
          result = `<a href="${BASEURL + item[label].get_profileurl()}">${item[
            label
          ].get_fullname()}</a>`;
          break;
        case "status":
          result = `<span>
                <i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="${
                  this.statusIcons[item[label]].tooltip
                }">${this.statusIcons[item[label]].icon}</i>
              </span>`;
          break;
        default:
          result = item[label];
          break;
      }

      return result;
    },
    editItem(item, index) {
      if (this.$listeners && this.$listeners.edit) {
        this.$emit("edit", {
          item: item,
          index: index,
        });
      } else {
        this.$router.push({
          name: "edit",
          params: {
            data: {
              index: index,
              item: item,
            },
          },
        });
        //window.location = BASEURL + "admin/" + this.module + "edit/" + (this.index_data ? item[this.index_data] : index);
      }
      return;
    },
    createItem() {
      this.$router.push({
        name: "edit",
        params: {
          data: {
            mode: "new"
          },
        },
      });
    },
    deleteItem(item, index) {
      if (this.$listeners && this.$listeners.delete) {
        this.$emit("delete", {
          item: item,
          index: index,
        });
      } else {
        this.tempDelete(item, index);
      }
      return;
    },
    archiveItem(item, index) {
      if (this.$listeners && this.$listeners.archive) {
        this.$emit("archive", {
          item: item,
          index: index,
        });
        return;
      } else {
        return;
      }
    },
    delete(item, index) {
      console.log({ item, index });
      return;
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
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        var instances = M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        var instances = M.Dropdown.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      if (this.endpoint) {
        this.getData();
      } else {
        this.loader = false;
      }
      this.$root.$on("eventing", (data) => {
        this.data[data.index] = data.item;
      });
    });
  },
});
