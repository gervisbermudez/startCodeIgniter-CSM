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
    pagination: {
      type: Boolean,
      required: false,
      default: false,
    },
    search_input: {
      type: Boolean,
      required: false,
      default: true,
    },
    show_empty_input: {
      type: Boolean,
      required: false,
      default: true,
    },
    module: {
      type: String,
      required: false,
    },
    preset_data: {
      type: Array,
      required: false,
      default: null,
    },
    options: {
      type: Array,
      required: false,
      default: null,
    },
  },
  data: function () {
    return {
      data: [],
      showPagination: false,
      paginator: {},
      paginatorLinks: [],
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
      toDeleteItem: {
        item: null,
        index: null,
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
    set_paginatorLinks: function () {
      if (this.showPagination) {
        let links = [];
        links.push({
          page: this.paginator.prev_page,
          label: '<i class="material-icons">chevron_left</i>',
          class: this.paginator.prev_page == 0 ? "disabled" : "waves-effect",
        });
        let pages = this.rangodepaginas(
          this.paginator.current_page - 0,
          2,
          this.paginator.total_pages
        );

        pages = this.paginaEllipsis(pages);

        if (!pages.includes(1)) {
          links.push({
            page: 1,
            label: 1,
            class: this.paginator.current_page == 1 ? "active" : "waves-effect",
          });
        }
        console.log({ pages });
        for (let index = 1; index <= pages.length; index++) {
          if (pages[index - 1] == "...") {
            if (index === 2) {
              links.push({
                page: parseInt(pages[index]) - 1,
                label: pages[index - 1],
                class: `waves-effect `,
              });
            } else {
              links.push({
                page: parseInt(pages[index - 2]) + 1,
                label: pages[index - 1],
                class: `waves-effect `,
              });
            }
          } else {
            links.push({
              page: pages[index - 1],
              label: pages[index - 1],
              class:
                this.paginator.current_page == pages[index - 1]
                  ? "active"
                  : "waves-effect",
            });
          }
        }
        if (!pages.includes(this.paginator.total_pages)) {
          links.push({
            page: this.paginator.total_pages,
            label: this.paginator.total_pages,
            class:
              this.paginator.current_page == this.paginator.total_pages
                ? "active"
                : "waves-effect",
          });
        }
        links.push({
          page: this.paginator.next_page,
          label: '<i class="material-icons">chevron_right</i>',
          class:
            this.paginator.next_page > this.paginator.total_pages
              ? "disabled"
              : "waves-effect",
        });

        return (this.paginatorLinks = links);
      }
      return null;
    },
    rangodepaginas: function (actual, rango, final) {
      var desde = actual - rango,
        hasta = actual + rango,
        paginas = [];
      for (var i = 1; i <= final; i++) {
        if (i === 1 || i === final || (i >= desde && i <= hasta)) {
          paginas.push(i);
        }
      }
      return paginas;
    },
    paginaEllipsis: function (paginas) {
      var final = paginas.pop();
      paginas.push(final);
      rango_con_ellipsis = [];
      for (var i = 1; i < paginas.length - 1; i++) {
        if (paginas[i - 1] === 1 && paginas[i] !== 2) {
          rango_con_ellipsis.push(1);
          rango_con_ellipsis.push("...");
        }
        rango_con_ellipsis.push(paginas[i]);

        if (paginas[i + 1] === final && paginas[i] !== final - 1) {
          rango_con_ellipsis.push("...");
          rango_con_ellipsis.push(final);
        }
      }
      return rango_con_ellipsis;
    },
    toggleEddit(configuration) {
      configuration.editable = !configuration.editable;
      this.$forceUpdate();
    },
    resetFilter: function () {
      this.filter = "";
    },
    pagerTo(page) {
      console.log(page);
      this.getData(page);
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
    getData: function (page = 1) {
      var self = this;
      self.loader = true;
      if (this.pagination) {
        var url = BASEURL + this.endpoint + "?page=" + page;
      } else {
        var url = BASEURL + this.endpoint;
      }
      fetch(url)
        .then((response) => response.json())
        .then((response) => {
          let data = response.data;
          if (response.current_page && this.pagination) {
            this.showPagination = true;
            this.paginator.current_page = response.current_page;
            this.paginator.per_page = response.per_page;
            this.paginator.total_rows = response.total_rows;
            this.paginator.offset = response.offset;
            this.paginator.total_pages = response.total_pages;
            this.paginator.first_page = response.first_page;
            this.paginator.last_page = response.last_page;
            this.paginator.next_page = response.next_page;
            this.paginator.prev_page = response.prev_page;
            this.set_paginatorLinks();
          }
          self.data = this.preProssesData(data);
          self.loader = false;
          this.initPlugins();
        })
        .catch((response) => {
          self.loader = false;
        });
    },
    preProssesData(data) {
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
      return data;
    },
    getContent(item, colum) {
      console.log(item, colum);
      let columName = colum.colum;
      if (colum.format && typeof colum.format == "function") {
        return colum.format(item, colum);
      }
      let result = "";
      switch (columName) {
        case "user":
          result = `<a href="${item[columName].get_profileurl()}">${item[
            columName
          ].get_fullname()}</a>`;
          break;
        case "status":
          result = `<span>
                <i class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="${
                  this.statusIcons[item[columName]].tooltip
                }">${this.statusIcons[item[columName]].icon}</i>
              </span>`;
          break;
        default:
          result = resolve(item, columName);
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
      if (this.$listeners && this.$listeners.new) {
        this.$emit("new");
      } else {
        this.$router.push({
          name: "edit",
          params: {
            data: {
              mode: "new",
            },
          },
        });
      }
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
    setToDeleteItem(item, index) {
      this.toDeleteItem = {
        item,
        index,
      };
      return;
    },
    confirmCallback(data) {
      console.log("confirmCallback", data);
      if (data) {
        this.deleteItem(this.toDeleteItem.item, this.toDeleteItem.index);
      }
    },
    archiveItem(item, index) {
      if (this.$listeners && this.$listeners.archive) {
        this.$emit("archive", {
          item: item,
          index: index,
        });
      } else {
        return;
      }
    },
    tempDelete: function (item, index) {
      this.toDeleteItem.item = item;
      this.toDeleteItem.index = index;
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
      } else if (this.preset_data) {
        this.data = this.preProssesData(this.preset_data);
        this.loader = false;
      } else {
        this.loader = false;
      }

      this.$root.$on("eventing", (data) => {
        this.data[data.index] = data.item;
      });
    });
  },
});
