// Definición del componente Vue llamado "dataTable"
var dataTable = Vue.component("dataTable", {
  // Plantilla HTML utilizada para mostrar los datos de la tabla
  template: "#dataTableComponent-template",

  // Propiedades que se utilizan para personalizar el comportamiento del componente
  props: {
    // Ruta de acceso a los datos de la tabla
    endpoint: {
      type: String,
      required: false,
    },
    // Nombre de la clave que contiene los datos en la respuesta
    index_data: {
      type: String,
      required: false,
    },
    // Indica si se deben ocultar los IDs de las filas en la tabla
    hideids: {
      type: Boolean,
      default: false,
    },
    // Personalización de las columnas de la tabla
    colums: {
      type: Array,
      required: false,
      default: null,
    },
    // Indica si se debe mostrar la paginación en la tabla
    pagination: {
      type: Boolean,
      required: false,
      default: false,
    },
    // Indica si se debe mostrar un campo de búsqueda en la tabla
    search_input: {
      type: Boolean,
      required: false,
      default: true,
    },
    // Indica si se debe mostrar un campo de búsqueda vacío al cargar la tabla
    show_empty_input: {
      type: Boolean,
      required: false,
      default: true,
    },
    // Nombre del módulo que se está utilizando
    module: {
      type: String,
      required: false,
    },
    // Datos predefinidos que se mostrarán en la tabla
    preset_data: {
      type: Array,
      required: false,
      default: null,
    },
    // Definición de las opciones de la tabla, como los botones de acción
    options: {
      type: Array,
      required: false,
      default: null,
    },
    show_fab: {
      type: Boolean,
      default: true,
    },
    fab_accent: {
      type: Boolean,
      default: false,
    },
    fab_tooltip: {
      type: String,
      default: "",
    },
    empty_title: {
      type: String,
      default: "",
    },
    empty_cta: {
      type: String,
      default: "",
    },
    empty_href: {
      type: String,
      default: "",
    },
    confirm_title: {
      type: String,
      default: "",
    },
    confirm_body: {
      type: String,
      default: "",
    },
    query_params: {
      type: Object,
      default: function () {
        return {};
      },
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
  watch: {
    loader(newVal, oldVal) {
      console.log('[DataTable WATCH] loader changed from', oldVal, 'to', newVal);
      this.$nextTick(() => {
        console.log('[DataTable WATCH] After nextTick, loader is:', this.loader);
        const loaderEl = this.$el.querySelector('.col.s12.center');
        if (loaderEl) {
          console.log('[DataTable WATCH] Loader element style.display:', loaderEl.style.display);
          console.log('[DataTable WATCH] Loader element computed display:', window.getComputedStyle(loaderEl).display);
        } else {
          console.log('[DataTable WATCH] Loader element NOT FOUND');
        }
      });
    },
    data(newVal, oldVal) {
      console.log('[DataTable WATCH] data changed, new length:', newVal.length);
    }
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
    pagerTo(page) {
      this.getData(page);
    },
    initPlugins: function () {
      setTimeout(() => {
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible:not(#slide-out)");
        M.Collapsible.init(elems, {});
        var elems = document.querySelectorAll("select");
        M.FormSelect.init(elems, {});
      }, 3000);
    },
    getData: function (page) {
      var self = this;
      if (typeof page === "undefined") {
        page = this.currentPage || 1;
      }
      console.log('[DataTable] getData called, setting loader=true');
      self.loader = true;
      var url = BASEURL + this.endpoint;
      if (this.pagination) {
        var query = this.listQuery(this.query_params || {}, page);
        var params = [];
        Object.keys(query).forEach(function (key) {
          params.push(encodeURIComponent(key) + "=" + encodeURIComponent(query[key]));
        });
        url += (url.indexOf("?") >= 0 ? "&" : "?") + params.join("&");
      }
      console.log('[DataTable] Fetching from:', url);
      fetch(url)
        .then((response) => {
          console.log('[DataTable] Response received, status:', response.status);
          return response.json();
        })
        .then((response) => {
          console.log('[DataTable] Response parsed:', response);
          // Coerce response data to an array and handle missing pagination fields
          let data = Array.isArray(response.data) ? response.data : (response && response.data ? [response.data] : []);
          console.log('[DataTable] Data extracted:', data.length, 'items');
          if (this.pagination) {
            this.applyPaginatorFromResponse(response);
          }
          // Ensure loader is hidden even if preprocessing fails
          try {
            console.log('[DataTable] Processing data...');
            self.data = this.preProssesData(data);
            console.log('[DataTable] Data processed successfully, items:', self.data.length);
          } catch (e) {
            console.error('[DataTable] preProssesData error:', e);
            self.data = Array.isArray(data) ? data : [];
          }
          console.log('[DataTable] Setting loader=false');
          self.loader = false;
          console.log('[DataTable] Loader set to:', self.loader);
          self.$nextTick(() => {
            console.log('[DataTable] After nextTick in getData, loader:', self.loader);
            const loaderDiv = self.$el.querySelector('.col.s12.center');
            const allLoaders = self.$el.querySelectorAll('.col.s12.center');
            console.log('[DataTable] Total loader elements found:', allLoaders.length);
            allLoaders.forEach((el, idx) => {
              console.log(`[DataTable] Loader ${idx}: display="${el.style.display}", computed="${window.getComputedStyle(el).display}", visible="${el.offsetHeight > 0}"`);
            });
            if (loaderDiv) {
              console.log('[DataTable] First loader div found, display:', loaderDiv.style.display);
              console.log('[DataTable] First loader div computed display:', window.getComputedStyle(loaderDiv).display);
              console.log('[DataTable] First loader div offsetHeight:', loaderDiv.offsetHeight, 'visible:', loaderDiv.offsetHeight > 0);
            }
            console.log('[DataTable] Data length:', self.data.length);
            console.log('[DataTable] ShowPagination:', self.showPagination);
          });
          this.initPlugins();
        })
        .catch((error) => {
          console.error('[DataTable] Fetch error:', error);
          self.loader = false;
          self.$forceUpdate();
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
    runOption(option, index, item) {
      if (option.action === "delete") {
        this.setToDeleteItem(item, index);
        return;
      }
      if (option.action === "archive") {
        this.archiveItem(item, index);
        return;
      }
      if (option.handler && this.$root && typeof this.$root[option.handler] === "function") {
        this.$root[option.handler]({ item: item, index: index, option: option });
        return;
      }
      if (option.url) {
        var url = option.url;
        if (option.url_param && item[option.url_param] !== undefined) {
          url += item[option.url_param];
        }
        window.location = url.indexOf("http") === 0 ? url : BASEURL + url;
        return;
      }
      if (option.href) {
        this.routerPush({ option: option, index: index, item: item });
      }
    },
    routerPush({ option, index, item }) {
      let params = {};

      if (option.params) {
        option.params.forEach((param) => {
          params[param] = item[param];
        });
      }

      this.$router.push({
        name: option.href,
        params: {
          ...params,
          data: {
            index,
            item,
          },
        },
      });
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
    console.log('[DataTable] Component mounted, initial loader:', this.loader);
    this.$nextTick(function () {
      console.log('[DataTable] In mounted nextTick, endpoint:', this.endpoint);
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
