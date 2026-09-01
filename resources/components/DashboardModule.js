var DashboardModule = new Vue({
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
    events: [],
    api_data: {
      dashboard: BASEURL + "api/v1/dashboard/",
    },
    graphs: {
      devices: { porcentajeMayor: "", labelMayor: "" },
      urlFrecuentes: { porcentajeMayor: "", labelMayor: "", valorMasAlto: "" },
    },
    stats: {
      totalVisitors: 0,
      visitorGrowth: 0,
      totalRequests: 0,
      requestGrowth: 0
    },
    kpis: {
      uniqueVisitors: 0,
      bounceRate: 0,
      pagesPerSession: 0,
      dailyGrowth: 0,
      todayVisits: 0,
      yesterdayVisits: 0,
      sessions: 0,
      totalVisits: 0,
    },
    topPages: {},
    referrers: { labels: [], datasets: [{ data: [] }] },
    canViewAnalytics: false,
    hasAnalyticsData: false,
    analyticsUrl: (typeof BASEURL !== "undefined" ? BASEURL : "/") + "admin/analytics",
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
      mode: "page",
      saving: false,
    },
    counts: {
      users: 0,
      pages: 0,
      files: 0,
      events: 0,
      albumes: 0,
      content: 0,
    },
    capabilities:
      typeof DASHBOARD_CAPS !== "undefined" && DASHBOARD_CAPS
        ? DASHBOARD_CAPS
        : {},
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
            link: `${BASEURL}admin/pages/editar/${page.page_id}`,
          };
        });
    },
    creatorModes: function () {
      var caps = this.capabilities || {};
      var required = {
        page: "create_page",
        album: "create_gallery",
        categorie: "create_categorie",
        fragment: "create_fragment",
      };
      return this.creator.modes.filter(function (mode) {
        return !!caps[required[mode]];
      });
    },
    hasReferrers: function () {
      return !!(
        this.referrers &&
        this.referrers.labels &&
        this.referrers.labels.length
      );
    },
  },
  methods: {
    setCreatorMode: function (mode) {
      if (this.creatorModes.indexOf(mode) === -1) {
        return;
      }
      this.creator.mode = mode;
    },
    creatorModeTip: function (mode) {
      var keys = {
        page: "tooltip_new_page",
        album: "tooltip_new_album",
        categorie: "tooltip_new_category",
        fragment: "menu_fragments",
      };
      return typeof lang === "function" ? lang(keys[mode] || mode) : mode;
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
      const raw =
        typeof CURRENT_USER !== "undefined" && CURRENT_USER
          ? CURRENT_USER
          : {};
      return {
        content,
        title,
        path,
        currentUser: new User(raw),
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
      if (this.creator.saving) return;
      if (this.creatorModes.indexOf(this.creator.mode) === -1) return;
      
      // Sanitizar contenido básico (prevenir tags peligrosos)
      const sanitizedContent = this.creator.content
        .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
        .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '')
        .replace(/on\w+\s*=\s*["'][^"']*["']/gi, '');
      
      this.creator.saving = true;
      let data = {};
      let url = "";

      switch (this.creator.mode) {
        case "page":
          data = this.getPageObject();
          data.content = sanitizedContent;
          url = `${BASEURL}api/v1/pages/`;

          break;
        case "album":
          data = this.getAlbumObject();
          url = `${BASEURL}api/v1/albumes/`;

          break;
        case "categorie":
          data = this.getCategorieObject();
          url = `${BASEURL}api/v1/categories/`;

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
          if (response && response.code == 200 && response.data) {
            switch (self.creator.mode) {
              case "page":
                window.location.href = `${BASEURL}admin/pages/edit/${response.data.page_id}`;
                break;
              case "album":
                window.location.href = `${BASEURL}admin/gallery/edit/${response.data.album_id}`;
                break;
              case "categorie":
                window.location.href = `${BASEURL}admin/categories/edit/${response.data.categorie_id}`;
                break;
              case "fragment":
                window.location.href = `${BASEURL}admin/fragments/edit/${response.data.fragment_id}`;
                break;
              default:
                self.creator.saving = false;
                break;
            }
          } else {
            self.creator.saving = false;
            M.toast({ html: (typeof lang === "function" && lang("dashboard_save_error")) || "An unexpected error occurred" });
          }
        },
        error: function (response) {
          self.loader = false;
          self.creator.saving = false;
          M.toast({ html: (typeof lang === "function" && lang("dashboard_save_error")) || "An unexpected error occurred" });
          console.error(response);
        },
      });
    },
    initStaticPlugins() {
      var fabs = document.querySelectorAll(".fixed-action-btn");
      if (fabs.length && typeof M.FloatingActionButton !== "undefined") {
        M.FloatingActionButton.init(fabs, {});
      }
      var fabTips = document.querySelectorAll(".fixed-action-btn .tooltipped");
      if (fabTips.length) {
        M.Tooltip.init(fabTips, {});
      }
    },
    init() {
      setTimeout(() => {
        var elems = document.querySelectorAll(".dropdown-trigger");
        M.Dropdown.init(elems, {});
        var elems = document.querySelectorAll(".collapsible:not(#slide-out)");
        M.Collapsible.init(elems, {});
        var elems = document.querySelectorAll(".tooltipped");
        M.Tooltip.init(elems, {});
      }, 500);
    },
    createChart: function (id, chartData) {
      if (typeof Chart === "undefined") {
        return;
      }
      var canvas = document.getElementById(id);
      if (
        !canvas ||
        !chartData ||
        !chartData.data ||
        !chartData.data.labels ||
        !chartData.data.labels.length
      ) {
        return;
      }
      if (typeof Chart.getChart === "function") {
        var existing = Chart.getChart(canvas);
        if (existing) {
          existing.destroy();
        }
      }
      new Chart(canvas, {
        type: chartData.type,
        data: chartData.data,
        options: {
          plugins: {
            legend: {
              display: false,
            },
          },
          scales: {
            x: {
              display: chartData.displayX || false,
              grid: {
                display: chartData.displayGrid || false,
              },
            },
            y: {
              display: chartData.displayY || false,
              grid: {
                display: chartData.displayGrid || false,
              },
            },
          },
        },
      });
    },
    calcularPorcentajeMayor: function (input) {
      if (!input || !input.labels || !input.datasets || !input.datasets[0]) {
        return { porcentajeMayor: "0", labelMayor: "", valorMasAlto: "" };
      }
      const { labels, datasets } = input;
      const data = datasets[0].data || [];
      if (!data.length) {
        return { porcentajeMayor: "0", labelMayor: "", valorMasAlto: "" };
      }
      const total = data.reduce((acc, curr) => acc + curr, 0) || 1;
      const mayor = Math.max.apply(null, data);
      const indexMayor = data.indexOf(mayor);
      const porcentajeMayor = ((mayor / total) * 100).toFixed(0);
      const labelMayor = labels[indexMayor] || "";
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
              link: `${BASEURL}admin/pages/view/${el.page_id}`,
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
      fetch(this.api_data.dashboard, {
        credentials: "same-origin",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((response) => {
          let data = response.data || {};
          if (data.capabilities) {
            this.capabilities = data.capabilities;
          }
          if (data.counts) {
            this.counts = Object.assign({}, this.counts, data.counts);
          }
          this.users = data.users
            ? data.users.map((user) => new User(user))
            : [];
          this.pages = data.pages
            ? data.pages.map((page) => {
              page.user = new User(page.user || {});
              return page;
            })
            : [];
          this.forms_types = data.collections || data.forms_types || [];
          this.content = data.content
            ? data.content.map((element) => {
              element.user = new User(element.user || {});
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
              album.user = new User(album.user || {});
              return album;
            })
            : [];
          this.events = data.events ? data.events : [];

          if (typeof data.can_view_analytics !== "undefined") {
            this.canViewAnalytics = !!data.can_view_analytics;
          }
          this.hasAnalyticsData = !!data.has_analytics_data || !!data.has_data;

          if (data.stats) {
            this.stats = data.stats;
          }

          if (data.kpis) {
            this.kpis = Object.assign({}, this.kpis, data.kpis);
          }

          if (data.topPages && !Array.isArray(data.topPages)) {
            this.topPages = data.topPages;
          } else if (data.topPages && Array.isArray(data.topPages)) {
            var mapped = {};
            data.topPages.forEach(function (row) {
              if (row && row.page_name) {
                mapped[row.page_name] = row.visits;
              }
            });
            this.topPages = mapped;
          } else {
            this.topPages = {};
          }

          if (data.referrers) {
            this.referrers = data.referrers;
          }

          this.loader = false;

          var self = this;
          this.$nextTick(function () {
            if (data.chart3 && data.chart3.labels && data.chart3.labels.length) {
              self.graphs.devices = self.calcularPorcentajeMayor(data.chart3);
              self.createChart("myChart3", {
                type: "bar",
                data: data.chart3,
                displayX: false,
                displayY: false,
              });
            }

            if (data.chart4 && data.chart4.labels && data.chart4.labels.length) {
              self.graphs.urlFrecuentes = self.calcularPorcentajeMayor(data.chart4);
              self.createChart("myChart4", {
                type: "doughnut",
                data: data.chart4,
                displayX: false,
                displayY: false,
              });
            }

            if (data.timeline) {
              self.timeline = self.getTimeLine(data.timeline);
            }

            if (data.chart1 && data.chart1.labels && data.chart1.labels.length) {
              self.createChart("myChart1", {
                type: "line",
                data: data.chart1,
                displayGrid: false,
              });
            }

            if (data.chart2 && data.chart2.labels && data.chart2.labels.length) {
              self.createChart("myChart2", {
                type: "bar",
                data: data.chart2,
                displayGrid: false,
              });
            }

            if (data.referrers && data.referrers.labels && data.referrers.labels.length) {
              self.createChart("myChartReferrers", {
                type: "doughnut",
                data: data.referrers,
                displayX: false,
                displayY: false,
              });
            }

            self.init();
          });
        })
        .catch((error) => {
          console.error("Dashboard data error:", error);
          this.loader = false;
          if (typeof M !== "undefined") {
            M.toast({
              html:
                (typeof lang === "function" && lang("dashboard_load_error")) ||
                "Error loading dashboard data. Please refresh the page.",
              classes: "red",
            });
          }
        });
    },
  },
  mounted: function () {
    this.$nextTick(() => {
      if (
        this.creatorModes.length &&
        this.creatorModes.indexOf(this.creator.mode) === -1
      ) {
        this.creator.mode = this.creatorModes[0];
      }
      this.initStaticPlugins();
      this.getDashboardData();
    });
  },
});
