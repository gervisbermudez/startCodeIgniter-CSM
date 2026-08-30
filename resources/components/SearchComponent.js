var SearchComponent = new Vue({
  el: "#root",
  data: {
    loader: true,
    searchTerm: "",
    typeFilter: "all",
    hits: [],
    i18n: window.SEARCH_I18N || {},
  },
  mixins: [mixins],
  computed: {
    queryTrimmed: function () {
      return (this.searchTerm || "").trim();
    },
    visibleHits: function () {
      if (this.typeFilter === "all") {
        return this.hits;
      }
      var self = this;
      return this.hits.filter(function (hit) {
        return hit.type === self.typeFilter;
      });
    },
    chipTypes: function () {
      var counts = {};
      this.hits.forEach(function (hit) {
        counts[hit.type] = (counts[hit.type] || 0) + 1;
      });
      var chips = [
        {
          id: "all",
          label: this.i18n.type_all || "All",
          count: this.hits.length,
        },
      ];
      var self = this;
      Object.keys(counts).forEach(function (type) {
        chips.push({
          id: type,
          label: self.i18n["type_" + type] || type,
          count: counts[type],
        });
      });
      return chips;
    },
    showIdle: function () {
      return this.queryTrimmed.length === 0;
    },
    showMinChars: function () {
      return this.queryTrimmed.length === 1;
    },
    showNoResults: function () {
      return this.queryTrimmed.length >= 2 && this.visibleHits.length === 0;
    },
    noResultsLabel: function () {
      var tpl = this.i18n.noResults || "%s";
      return tpl.replace("%s", this.queryTrimmed);
    },
    resultsCountLabel: function () {
      var tpl = this.i18n.resultsCount || "%s";
      return tpl.replace("%s", String(this.visibleHits.length));
    },
  },
  methods: {
    setTypeFilter: function (id) {
      this.typeFilter = id;
    },
    clearSearch: function () {
      this.searchTerm = "";
      this.hits = [];
      this.typeFilter = "all";
      if (window.history && window.history.replaceState) {
        window.history.replaceState({}, "", BASEURL + "admin/search/");
      }
    },
    performSearch: function () {
      var self = this;
      var term = this.queryTrimmed;
      if (term.length < 2) {
        this.hits = [];
        this.loader = false;
        return;
      }
      this.loader = true;
      if (window.history && window.history.replaceState) {
        window.history.replaceState(
          {},
          "",
          BASEURL + "admin/search/?q=" + encodeURIComponent(term)
        );
      }
      $.ajax({
        type: "GET",
        url: BASEURL + "api/v1/search/?q=" + encodeURIComponent(term),
        dataType: "json",
        success: function (response) {
          var data =
            response && response.data
              ? response.data
              : window.SearchMapper.emptyPayload();
          self.hits = window.SearchMapper.flatten(data, self.i18n);
          self.loader = false;
          self.typeFilter = "all";
        },
        error: function (error) {
          self.loader = false;
          M.toast({ html: self.i18n.error || "Error" });
          console.error(error);
        },
      });
    },
  },
  mounted: function () {
    var self = this;
    this.$nextTick(function () {
      var urlParams = new URLSearchParams(window.location.search);
      var q = urlParams.get("q");
      if (q) {
        self.searchTerm = q;
        self.performSearch();
      } else {
        self.loader = false;
      }
    });
  },
});
