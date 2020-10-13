var dashboardModule = new Vue({
  el: "#root",
  data: {
    debug: DEBUGMODE,
    loader: true,
    users: [],
    files: [],
    pages: [],
    forms_types: [],
    albumes: [],
    content: [],
    api_data: {
      dashboard: BASEURL + "api/v1/dashboard/",
    },
  },
  mixins: [mixins],
  methods: {
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
      }, 3000);
    },
  },
  mounted: function () {
    this.$nextTick(function () {
      Promise.all([
        fetch(this.api_data.dashboard).then((response) => response.json()),
      ]).then(([dashboard_response]) => {
        this.users = dashboard_response.data.users.map((user) => new User(user));
        this.pages = dashboard_response.data.pages.map((page) => {
          page.user = new User(page.user);
          return page;
        });
        this.forms_types = dashboard_response.data.forms_types;
        this.content = dashboard_response.data.content.map((element) => {
          element.user = new User(element.user);
          element.status = element.status == "1";
          return element;
        });
        this.files = dashboard_response.data.files.map((file) => {
          return new ExplorerFile(file);
        });
        this.albumes = dashboard_response.data.albumes.map((album) => {
          album.user = new User(album.user);
          return album;
        });
        this.loader = false;
        this.init();
      });
    });
  },
});
