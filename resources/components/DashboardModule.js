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
    layout: [],
    draftLayout: [],
    catalog: [],
    layoutEditing: false,
    canEditLayout: !!(
      typeof DASHBOARD_CAPS !== "undefined" &&
      DASHBOARD_CAPS &&
      DASHBOARD_CAPS.can_edit_layout
    ),
    layoutSource: "default",
    layoutSaving: false,
    pickerOpen: false,
    pickerRi: 0,
    pickerCi: 0,
    columnWidths: [3, 4, 5, 6, 7, 12],
    chartPayload: null,
    defaultAvatar:
      (typeof BASEURL !== "undefined" ? BASEURL : "/") +
      "public/img/profile/default_profile_2.jpg",
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
    visibleLayout: function () {
      return this.layoutEditing ? this.draftLayout : this.layout;
    },
    visibleRows: function () {
      var layout = this.visibleLayout || {};
      return layout.rows || [];
    },
    layoutIsEmpty: function () {
      return !(this.visibleRows || []).some(function (row) {
        return (row.cols || []).some(function (col) {
          return col.items && col.items.length;
        });
      });
    },
    addableWidgets: function () {
      var ids = this.collectIds(this.draftLayout);
      return (this.catalog || []).filter(function (w) {
        return !ids[w.id];
      });
    },
  },
  methods: {
    widgetTitle: function (item) {
      if (!item) return "";
      if (item.title) return item.title;
      var key = item.lang || item.id;
      return typeof lang === "function" ? lang(key) : key;
    },
    widgetActionLabel: function (key) {
      return typeof lang === "function" ? lang(key) : key;
    },
    widgetTotal: function (item) {
      if (!item) return undefined;
      var map = {
        users: "users",
        files: "files",
        albums: "albumes",
        collections: "content",
      };
      var key = map[item.id];
      return key ? this.counts[key] : undefined;
    },
    refreshVisibleWidgets: function () {
      var self = this;
      this.$nextTick(function () {
        self.renderCharts();
        self.init();
      });
    },
    collectIds: function (layout) {
      var ids = {};
      ((layout && layout.rows) || []).forEach(function (row) {
        (row.cols || []).forEach(function (col) {
          (col.items || []).forEach(function (item) {
            if (item && item.id) {
              ids[item.id] = true;
            }
          });
        });
      });
      return ids;
    },
    asGrid: function (layout) {
      if (layout && layout.rows) {
        return layout;
      }
      return { v: 2, rows: [] };
    },
    cloneLayout: function (layout) {
      try {
        return JSON.parse(JSON.stringify(this.asGrid(layout)));
      } catch (e) {
        return { v: 2, rows: [] };
      }
    },
    clampWidth: function (w) {
      w = parseInt(w, 10);
      if (this.columnWidths.indexOf(w) !== -1) {
        return w;
      }
      return 12;
    },
    colClass: function (w, editing) {
      w = this.clampWidth(w);
      if (editing) {
        return "col s" + w;
      }
      return "col s12 l" + w;
    },
    emptyCol: function (w) {
      w = this.clampWidth(w || 12);
      return { w: w, col: this.colClass(w, true), items: [] };
    },
    rebalanceRow: function (row) {
      var n = (row.cols || []).length;
      var w = n <= 1 ? 12 : n === 2 ? 6 : 4;
      var self = this;
      row.cols = (row.cols || []).map(function (col) {
        return Object.assign({}, col, { w: w, col: self.colClass(w, true) });
      });
    },
    mutateDraft: function (fn) {
      var layout = this.cloneLayout(this.draftLayout);
      fn.call(this, layout);
      this.draftLayout = layout;
      this.refreshVisibleWidgets();
    },
    catalogWidget: function (id) {
      var found = null;
      (this.catalog || []).some(function (w) {
        if (w.id === id) {
          found = w;
          return true;
        }
        return false;
      });
      return found;
    },
    widgetBind: function (item) {
      return {
        users: this.users,
        files: this.files,
        albumes: this.albumes,
        forms_types: this.forms_types,
        content: this.content,
        total: this.widgetTotal(item),
        kpis: this.kpis,
        analyticsUrl: this.analyticsUrl,
        canViewAnalytics: this.canViewAnalytics,
        hasAnalyticsData: this.hasAnalyticsData,
        hasReferrers: this.hasReferrers,
        stats: this.stats,
        graphs: this.graphs,
        topPages: this.topPages,
        loader: this.loader,
        counts: this.counts,
        creator: this.creator,
        creatorModes: this.creatorModes,
        drafts: this.pages_draf,
        timeline: this.timeline,
        defaultAvatar: this.defaultAvatar,
      };
    },
    applyLayoutPayload: function (data) {
      if (!data) return;
      this.layout = this.asGrid(data.layout);
      this.catalog = data.catalog || [];
      this.layoutSource = data.layout_source || data.source || "default";
      if (typeof data.can_edit_layout !== "undefined") {
        this.canEditLayout = !!data.can_edit_layout;
      }
      this.draftLayout = this.cloneLayout(this.layout);
    },
    startEditLayout: function () {
      if (!this.canEditLayout) return;
      this.draftLayout = this.cloneLayout(this.layout);
      this.layoutEditing = true;
      this.closePicker();
      this.refreshVisibleWidgets();
    },
    cancelEditLayout: function () {
      this.draftLayout = this.cloneLayout(this.layout);
      this.layoutEditing = false;
      this.closePicker();
    },
    onPickerKey: function (evt) {
      if (evt && evt.key === "Escape") {
        this.closePicker();
      }
    },
    openPicker: function (ri, ci) {
      this.pickerRi = ri;
      this.pickerCi = ci;
      if (!this.pickerOpen) {
        document.addEventListener("keydown", this.onPickerKey);
      }
      this.pickerOpen = true;
    },
    closePicker: function () {
      this.pickerOpen = false;
      document.removeEventListener("keydown", this.onPickerKey);
    },
    pickWidget: function (id) {
      this.addWidgetToColumn(this.pickerRi, this.pickerCi, id);
      if (!this.addableWidgets.length) {
        this.closePicker();
      }
    },
    addRow: function () {
      this.mutateDraft(function (layout) {
        layout.rows.push({ cols: [this.emptyCol(12)] });
      });
    },
    removeRow: function (ri) {
      this.mutateDraft(function (layout) {
        layout.rows.splice(ri, 1);
      });
    },
    moveRow: function (ri, dir) {
      this.mutateDraft(function (layout) {
        var next = ri + dir;
        if (next < 0 || next >= layout.rows.length) return;
        var tmp = layout.rows[ri];
        layout.rows[ri] = layout.rows[next];
        layout.rows[next] = tmp;
      });
    },
    addColumn: function (ri) {
      this.mutateDraft(function (layout) {
        var row = layout.rows[ri];
        if (!row || (row.cols || []).length >= 3) return;
        row.cols.push(this.emptyCol(4));
        this.rebalanceRow(row);
      });
    },
    removeColumn: function (ri, ci) {
      this.mutateDraft(function (layout) {
        var row = layout.rows[ri];
        if (!row) return;
        row.cols.splice(ci, 1);
        if (!row.cols.length) {
          layout.rows.splice(ri, 1);
        } else {
          this.rebalanceRow(row);
        }
      });
    },
    setColumnWidth: function (ri, ci, w) {
      var self = this;
      this.mutateDraft(function (layout) {
        var col = layout.rows[ri] && layout.rows[ri].cols[ci];
        if (!col) return;
        col.w = self.clampWidth(w);
        col.col = self.colClass(col.w, true);
      });
    },
    addWidgetToColumn: function (ri, ci, evt) {
      var id = evt && evt.target ? evt.target.value : evt;
      if (evt && evt.target) {
        evt.target.value = "";
      }
      if (!id) return;
      var found = this.catalogWidget(id);
      if (!found) return;
      this.mutateDraft(function (layout) {
        if (this.collectIds(layout)[id]) return;
        var col = layout.rows[ri] && layout.rows[ri].cols[ci];
        if (!col) return;
        col.items = (col.items || []).concat([
          {
            id: found.id,
            component: found.component,
            lang: found.lang,
            title: found.title,
            icon: found.icon,
          },
        ]);
      });
    },
    removeWidget: function (ri, ci, id) {
      this.mutateDraft(function (layout) {
        var col = layout.rows[ri] && layout.rows[ri].cols[ci];
        if (!col) return;
        col.items = (col.items || []).filter(function (w) {
          return w.id !== id;
        });
      });
    },
    moveWidgetInColumn: function (ri, ci, wi, dir) {
      this.mutateDraft(function (layout) {
        var col = layout.rows[ri] && layout.rows[ri].cols[ci];
        if (!col || !col.items) return;
        var next = wi + dir;
        if (next < 0 || next >= col.items.length) return;
        var tmp = col.items[wi];
        col.items[wi] = col.items[next];
        col.items[next] = tmp;
      });
    },
    moveWidgetAcross: function (ri, ci, wi, dci) {
      this.mutateDraft(function (layout) {
        var row = layout.rows[ri];
        if (!row) return;
        var dest = ci + dci;
        if (dest < 0 || dest >= row.cols.length) return;
        var col = row.cols[ci];
        var item = col.items && col.items[wi];
        if (!item) return;
        col.items.splice(wi, 1);
        row.cols[dest].items = (row.cols[dest].items || []).concat([item]);
      });
    },
    slimDraft: function () {
      var rows = [];
      ((this.draftLayout && this.draftLayout.rows) || []).forEach(function (row) {
        var cols = [];
        (row.cols || []).forEach(function (col) {
          var items = (col.items || [])
            .map(function (w) {
              return w.id;
            })
            .filter(Boolean);
          if (items.length) {
            cols.push({ w: col.w, items: items });
          }
        });
        if (cols.length) {
          rows.push({ cols: cols });
        }
      });
      return { v: 2, rows: rows };
    },
    postLayout: function (url, body, onOk) {
      var self = this;
      if (this.layoutSaving) return;
      this.layoutSaving = true;
      fetch(url, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: body ? JSON.stringify(body) : "{}",
      })
        .then(function (response) {
          return response.json().then(function (json) {
            return { status: response.status, json: json };
          });
        })
        .then(function (res) {
          self.layoutSaving = false;
          if (res.status === 403) {
            M.toast({
              html:
                (typeof lang === "function" && lang("dashboard_layout_forbidden")) ||
                "You cannot change this layout",
              classes: "red",
            });
            return;
          }
          if (res.json && res.json.code == 200 && res.json.data) {
            onOk(res.json.data);
          } else {
            M.toast({
              html:
                (typeof lang === "function" && lang("dashboard_save_error")) ||
                "An unexpected error occurred",
            });
          }
        })
        .catch(function () {
          self.layoutSaving = false;
          M.toast({
            html:
              (typeof lang === "function" && lang("dashboard_save_error")) ||
              "An unexpected error occurred",
          });
        });
    },
    saveLayout: function () {
      var self = this;
      this.postLayout(this.api_data.dashboard + "layout", { layout: this.slimDraft() }, function (data) {
        self.applyLayoutPayload(data);
        self.layoutEditing = false;
        self.closePicker();
        M.toast({
          html:
            (typeof lang === "function" && lang("dashboard_layout_saved")) ||
            "Layout saved",
        });
        self.$nextTick(function () {
          self.renderCharts();
          self.init();
        });
      });
    },
    resetLayout: function () {
      var self = this;
      this.postLayout(this.api_data.dashboard + "layout_reset", {}, function (data) {
        self.applyLayoutPayload(data);
        self.layoutEditing = false;
        self.closePicker();
        M.toast({
          html:
            (typeof lang === "function" && lang("dashboard_layout_saved")) ||
            "Layout saved",
        });
        self.$nextTick(function () {
          self.renderCharts();
          self.init();
        });
      });
    },
    renderCharts: function () {
      var data = this.chartPayload || {};
      if (data.chart3 && data.chart3.labels && data.chart3.labels.length) {
        this.graphs.devices = this.calcularPorcentajeMayor(data.chart3);
        this.createChart("myChart3", {
          type: "bar",
          data: data.chart3,
          displayX: false,
          displayY: false,
        });
      }
      if (data.chart4 && data.chart4.labels && data.chart4.labels.length) {
        this.graphs.urlFrecuentes = this.calcularPorcentajeMayor(data.chart4);
        this.createChart("myChart4", {
          type: "doughnut",
          data: data.chart4,
          displayX: false,
          displayY: false,
        });
      }
      if (data.chart1 && data.chart1.labels && data.chart1.labels.length) {
        this.createChart("myChart1", {
          type: "line",
          data: data.chart1,
          displayGrid: false,
        });
      }
      if (data.chart2 && data.chart2.labels && data.chart2.labels.length) {
        this.createChart("myChart2", {
          type: "bar",
          data: data.chart2,
          displayGrid: false,
        });
      }
      if (data.referrers && data.referrers.labels && data.referrers.labels.length) {
        this.createChart("myChartReferrers", {
          type: "doughnut",
          data: data.referrers,
          displayX: false,
          displayY: false,
        });
      }
    },
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
          this.applyLayoutPayload(data);
          this.chartPayload = data;
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
          if (data.timeline) {
            this.timeline = this.getTimeLine(data.timeline);
          }
          this.$nextTick(function () {
            self.renderCharts();
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
  beforeDestroy: function () {
    document.removeEventListener("keydown", this.onPickerKey);
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
