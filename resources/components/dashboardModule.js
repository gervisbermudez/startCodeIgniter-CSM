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
    graphs: {
      devices: { porcentajeMayor: "", labelMayor: "" },
      urlFrecuentes: { porcentajeMayor: "", labelMayor: "", valorMasAlto: "" },
    },
    timeline: [],
    creator: {
      modes: ["page", "album", "categorie", "fragment"],
      icons: {
        page: "web",
        album: "perm_media",
        categorie: "receipt",
        fragment: "bookmark_border",
      },
      content: "",
      mode: "page", // page, album, categorie, fragment
    },
  },
  mixins: [mixins],
  computed: {
    pages_draf: function () {
      return this.pages
        .filter((page) => {
          return page.status == "2";
        })
        .slice(0, 5)
        .map((page) => {
          return {
            ...page,
            link: `${BASEURL}admin/paginas/editar/${page.page_id}`,
          };
        });
    },
  },
  methods: {
    setCreatorMode: function (mode) {
      this.creator.mode = mode;
    },
    string_to_slug: function (str) {
      if (str.length == 0) return "";

      str = str.replace(/^\s+|\s+$/g, ""); // trim

      str = str.toLowerCase();

      // remove accents, swap ñ for n, etc
      var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
      var to = "aaaaeeeeiiiioooouuuunc-/----";
      for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), "g"), to.charAt(i));
      }

      str = str
        .replace(/[^a-z0-9 -/]/g, "") // remove invalid chars
        .replace(/\s+/g, "-") // collapse whitespace and replace by -
        .replace(/-+/g, "-"); // collapse dashes

      return str;
    },
    getCreatorBasicData: function () {
      const content = this.creator.content;
      const title = content.substring(0, 40);
      const path = this.string_to_slug(title);
      const currentUser = new User(
        JSON.parse(localStorage.getItem("userdata"))
      );
      return {
        content,
        title,
        path,
        currentUser,
      };
    },
    getPageObject: function () {
      let { content, title, path, currentUser } = this.getCreatorBasicData();

      return {
        content,
        title,
        path,
        subtitle: "",
        page_type_id: 1,
        status: 2,
        json_content: [],
        publishondate: true,
        date_publish: null,
        visibility: 1,
        template: "default",
        layout: "default",
        categorie_id: 0,
        subcategorie_id: 0,
        mainImage: "",
        thumbnailImage: "",
        page_data: {
          title: title,
          meta: [
            {
              name: "author",
              content: currentUser.get_fullname(),
            },
            {
              name: "keywords",
              content: title,
            },
            {
              name: "description",
              content: content,
            },
            {
              name: "ROBOTS",
              content: "NOODP",
            },
            {
              name: "GOOGLEBOT",
              content: "INDEX, FOLLOW",
            },
            {
              property: "og:title",
              content: title,
            },
            {
              property: "og:description",
              content: "Content of the page",
            },
            {
              property: "og:site_name",
              content: currentUser.get_fullname(),
            },
            {
              property: "og:url",
              content: "",
            },
            {
              property: "og:image",
              content: "",
            },
            {
              property: "og:type",
              content: "article",
            },
            {
              name: "twitter:card",
              content: "summary_large_image",
            },
            {
              name: "twitter:site",
              content: "",
            },
            {
              name: "twitter:creator",
              content: "",
            },
            {
              name: "twitter:site",
              content: "",
            },
            {
              name: "twitter:title",
              content: title,
            },
            {
              name: "twitter:description",
              content: content,
            },
            {
              name: "twitter:image",
              content: "",
            },
          ],
        },
      };
    },
    getAlbumObject: function () {
      let { content, title } = this.getCreatorBasicData();

      return {
        name: title,
        description: content,
        status: 2,
        album_items: [],
      };
    },
    getCategorieObject: function () {
      let { content, title } = this.getCreatorBasicData();

      return {
        name: title,
        description: content,
        type: "page",
        parent_id: 0,
        status: 2,
      };
    },

    getFragmentsObjects: function () {
      let { content, title } = this.getCreatorBasicData();
      return {
        name: title,
        description: content,
        type: "parrafo",
        parent_id: 0,
        status: 2,
      };
    },

    saveDraft: function () {
      if (this.creator.content.length < 6) return;
      let data = {};
      let url = "";

      switch (this.creator.mode) {
        case "page":
          data = this.getPageObject();
          url = `${BASEURL}api/v1/pages/`;

          break;
        case "album":
          data = this.getAlbumObject();
          url = `${BASEURL}api/v1/albumes/`;

          break;
        case "categorie":
          data = this.getCategorieObject();
          url = `${BASEURL}api/v1/categorie/`;

          break;

        case "fragment":
          data = this.getFragmentsObjects();
          url = `${BASEURL}api/v1/fragments/`;

          break;
      }

      const method = "POST";
      var self = this;

      $.ajax({
        type: method,
        url: url,
        data: data,
        dataType: "json",
        success: (response) => {
          self.debug ? console.log(url, response) : null;
          if (response.code == 200) {
            switch (this.creator.mode) {
              case "page":
                window.location.href = `${BASEURL}admin/paginas/editar/${response.data.page_id}`;
                break;
              case "album":
                window.location.href = `${BASEURL}admin/galeria/editar/${response.data.album_id}`;
                break;
              case "categorie":
                window.location.href = `${BASEURL}admin/categorias/editar/${response.data.categorie_id}`;
                break;
              case "fragment":
                window.location.href = `${BASEURL}admin/Fragments/editar/${response.data.fragment_id}`;
              default:
                break;
            }
          }
        },
        error: function (response) {
          self.loader = false;
          M.toast({ html: "Ocurrió un error inesperado" });
          console.error(error);
        },
      });
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible");
        M.Collapsible.init(elems, {});
      }, 3000);
    },
    createChart: (id, chartData) => {
      // Function that creates a new chart with the provided data
      const ctx = document.getElementById(id); // Gets the DOM element with the provided id
      new Chart(ctx, {
        // Creates a new instance of the Chart class
        type: chartData.type, // Type of chart to be created (line, bar, pie, etc)
        data: chartData.data, // Data to be used to create the chart
        options: {
          plugins: {
            legend: {
              display: false, // Hides the chart legend
            },
          },
          scales: {
            x: {
              display: chartData.displayX || false, // Determines whether to show the X axis or not
              grid: {
                display: chartData.displayGrid || false, // Determines whether to show the grid lines on the X axis or not
              },
            },
            y: {
              display: chartData.displayY || false, // Determines whether to show the Y axis or not
              grid: {
                display: chartData.displayGrid || false, // Determines whether to show the grid lines on the Y axis or not
              },
            },
          },
        },
      });
    },
    calcularPorcentajeMayor: function (input) {
      const { labels, datasets } = input;
      const data = datasets[0].data;
      const total = data.reduce((acc, curr) => acc + curr, 0);
      const mayor = Math.max(...data);
      const indexMayor = data.indexOf(mayor);
      const porcentajeMayor = ((mayor / total) * 100).toFixed(0);
      const labelMayor = labels[indexMayor];
      const valorMasAlto = data[indexMayor];
      return { porcentajeMayor, labelMayor, valorMasAlto };
    },
    getTimeLine: function (data) {
      return data
        .map((el) => {
          if (el.page_id) {
            return {
              title: el.title,
              content: this.getcontentText(el.content),
              link: `${BASEURL}admin/paginas/view/${el.page_id}`,
              user: new User(el.user),
              date: this.timeAgo(el.date_create),
              imagen_file: el.imagen_file ? el.imagen_file : null,
              status: el.status,
            };
          }
        })
        .filter((e) => e && e.status == "1");
    },
    getDashboardData() {
      fetch(this.api_data.dashboard)
        .then((response) => response.json())
        .then((response) => {
          let data = response.data;
          this.users = data.users
            ? data.users.map((user) => new User(user))
            : [];
          this.pages = data.pages
            ? data.pages.map((page) => {
                page.user = new User(page.user);
                return page;
              })
            : [];
          this.forms_types = data.forms_types ? data.forms_types : [];
          this.content = data.content
            ? data.content.map((element) => {
                element.user = new User(element.user);
                element.status = element.status == "1";
                return element;
              })
            : [];
          this.files = data.files
            ? data.files.map((file) => {
                return new ExplorerFile(file);
              })
            : [];
          this.albumes = data.albumes
            ? data.albumes.map((album) => {
                album.user = new User(album.user);
                return album;
              })
            : [];
          this.loader = false;

          this.graphs.devices = this.calcularPorcentajeMayor(data.chart3);
          this.graphs.urlFrecuentes = this.calcularPorcentajeMayor(data.chart4);
          this.timeline = this.getTimeLine(data.timeline);
          this.createChart("myChart1", {
            type: "line",
            data: data.chart1,
            displayGrid: false,
          });
          this.createChart("myChart2", {
            type: "bar",
            data: data.chart2,
            displayGrid: false,
          });
          this.createChart("myChart3", {
            type: "bar",
            data: data.chart3,
            displayX: false,
            displayY: false,
          });
          this.createChart("myChart4", {
            type: "doughnut",
            data: data.chart4,
            displayX: false,
            displayY: false,
          });

          this.init();
        })
        .catch((error) => {
          console.error(error);
        });
    },
  },
  mounted: function () {
    this.$nextTick(() => {
      this.getDashboardData();
    });
  },
});
