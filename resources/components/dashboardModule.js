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
    getDashboardData() {
      fetch(this.api_data.dashboard)
        .then((response) => response.json())
        .then((response) => {
          let data = response.data;
          this.users = data.users ? data.users.map((user) => new User(user)) : [];
          this.pages = data.pages ? data.pages.map((page) => {
            page.user = new User(page.user);
            return page;
          }) : [];
          this.forms_types = data.forms_types ? data.forms_types : [];
          this.content = data.content ? data.content.map((element) => {
            element.user = new User(element.user);
            element.status = element.status == "1";
            return element;
          }) : [];
          this.files = data.files ? data.files.map((file) => {
            return new ExplorerFile(file);
          }) : [];
          this.albumes = data.albumes ? data.albumes.map((album) => {
            album.user = new User(album.user);
            return album;
          }) : [];
          this.loader = false;
          this.init();
        }).catch(error => {
          console.error(error);
        })
    }
  },
  mounted: function () {
    this.$nextTick(() => {
      this.getDashboardData();
    });
  },
});
