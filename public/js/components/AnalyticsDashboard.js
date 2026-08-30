/**
 * Analytics Dashboard (Vue 2)
 * Loads GET /api/v1/analytics/dashboard then polls realtime.
 * Chart.js must be loaded first (MY_Controller footer includes).
 */
if (document.getElementById("analytics-dashboard") && !window.AnalyticsDashboardMounted) {
  window.AnalyticsDashboardMounted = true;

  var analyticsRoot = document.getElementById("analytics-dashboard");

  function analyticsPad(n) {
    return n < 10 ? "0" + n : String(n);
  }

  function analyticsLocalDate(date) {
    return (
      date.getFullYear() +
      "-" +
      analyticsPad(date.getMonth() + 1) +
      "-" +
      analyticsPad(date.getDate())
    );
  }

  function analyticsDaysAgo(days) {
    var date = new Date();
    date.setDate(date.getDate() - days);
    return analyticsLocalDate(date);
  }

  var AnalyticsDashboard = new Vue({
    el: "#analytics-dashboard",
    mixins: typeof mixins !== "undefined" ? [mixins] : [],
    data: {
      loading: {
        overview: false,
        trend: false,
        pages: false,
        devices: false,
        realtime: false,
      },
      overview: {
        total_sessions: 0,
        total_pageviews: 0,
        unique_visitors: 0,
        avg_time_on_page: 0,
        bounce_rate: 0,
        conversion_rate: 0,
        pages_per_session: 0,
        mobile_visits: 0,
        desktop_visits: 0,
        tablet_visits: 0,
      },
      trendData: [],
      popularPages: [],
      deviceStats: [],
      trafficSources: [],
      realtimeVisitors: [],
      topEvents: [],
      pageId: analyticsRoot.getAttribute("data-page-id") || "",
      i18n: {
        sessions: analyticsRoot.getAttribute("data-i18n-sessions") || "Sessions",
        pageviews: analyticsRoot.getAttribute("data-i18n-pageviews") || "Pageviews",
        trend: analyticsRoot.getAttribute("data-i18n-trend") || "Traffic trend",
        devices: analyticsRoot.getAttribute("data-i18n-devices") || "Devices",
        topPages: analyticsRoot.getAttribute("data-i18n-top-pages") || "Top pages",
        noData: analyticsRoot.getAttribute("data-i18n-no-data") || "No data",
        unauthorized: analyticsRoot.getAttribute("data-i18n-unauthorized") || "Unauthorized",
      },
      dateRange: {
        start: analyticsDaysAgo(30),
        end: analyticsLocalDate(new Date()),
      },
      charts: {
        trend: null,
        devices: null,
        pages: null,
      },
      realtimeInterval: null,
      exporting: false,
    },
    computed: {
      formattedAvgTime: function () {
        var seconds = Math.round(this.overview.avg_time_on_page || 0);
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        return minutes + "m " + remainingSeconds + "s";
      },
      clearPageFilterUrl: function () {
        var base = typeof BASEURL !== "undefined" ? BASEURL : "/";
        return base + "admin/analytics";
      },
    },
    methods: {
      apiUrl: function (path) {
        var base = typeof BASEURL !== "undefined" ? BASEURL : "/";
        return base + "api/v1/analytics/" + path.replace(/^\//, "");
      },
      buildParams: function (extra) {
        var params = {
          start_date: this.dateRange.start,
          end_date: this.dateRange.end,
        };
        if (this.pageId) {
          params.page_id = this.pageId;
        }
        if (extra) {
          Object.keys(extra).forEach(function (key) {
            params[key] = extra[key];
          });
        }
        return new URLSearchParams(params).toString();
      },
      apiGet: function (path) {
        return fetch(this.apiUrl(path), {
          credentials: "same-origin",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        }).then(function (response) {
          if (response.status === 401) {
            return { code: 401, data: null };
          }
          return response.json();
        });
      },
      destroyChart: function (canvasId, key) {
        if (typeof Chart !== "undefined" && Chart.getChart) {
          var existing = Chart.getChart(canvasId);
          if (existing) {
            existing.destroy();
          }
        }
        if (this.charts[key]) {
          this.charts[key] = null;
        }
      },
      loadAllData: function () {
        var self = this;
        self.loading.overview = true;
        self.loading.trend = true;
        self.loading.pages = true;
        self.loading.devices = true;

        return self.apiGet("dashboard?" + self.buildParams()).then(function (data) {
          if (data && data.code === 200 && data.data) {
            var payload = data.data;
            if (payload.overview) {
              self.overview = payload.overview;
            }
            self.trendData = Array.isArray(payload.trend) ? payload.trend : [];
            self.popularPages = Array.isArray(payload.popular_pages) ? payload.popular_pages : [];
            self.deviceStats = Array.isArray(payload.devices) ? payload.devices : [];
            self.trafficSources = Array.isArray(payload.traffic_sources) ? payload.traffic_sources : [];
            self.topEvents = Array.isArray(payload.events) ? payload.events : [];
            self.realtimeVisitors = Array.isArray(payload.realtime) ? payload.realtime : [];
            self.$nextTick(function () {
              self.renderTrendChart();
              self.renderDeviceChart();
              self.renderPopularPagesChart();
            });
          }
        }).catch(function () {
          if (typeof M !== "undefined") {
            M.toast({ html: "Ocurrió un error inesperado" });
          }
        }).then(function () {
          self.loading.overview = false;
          self.loading.trend = false;
          self.loading.pages = false;
          self.loading.devices = false;
        });
      },
      loadRealtimeVisitors: function () {
        var self = this;
        var qs = self.pageId ? "?page_id=" + encodeURIComponent(self.pageId) : "";
        return self.apiGet("realtime" + qs).then(function (data) {
          if (data && data.code === 200 && data.data) {
            self.realtimeVisitors = Array.isArray(data.data) ? data.data : [];
          }
        });
      },
      renderTrendChart: function () {
        var canvas = document.getElementById("trendChart");
        this.destroyChart("trendChart", "trend");
        if (!canvas || !this.trendData.length || typeof Chart === "undefined") {
          return;
        }
        this.charts.trend = new Chart(canvas.getContext("2d"), {
          type: "line",
          data: {
            labels: this.trendData.map(function (d) {
              return d.date;
            }),
            datasets: [
              {
                label: this.i18n.sessions,
                data: this.trendData.map(function (d) {
                  return d.sessions;
                }),
                borderColor: "#4CAF50",
                backgroundColor: "rgba(76, 175, 80, 0.1)",
                tension: 0.4,
              },
              {
                label: this.i18n.pageviews,
                data: this.trendData.map(function (d) {
                  return d.pageviews;
                }),
                borderColor: "#2196F3",
                backgroundColor: "rgba(33, 150, 243, 0.1)",
                tension: 0.4,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true, position: "top" },
              title: { display: true, text: this.i18n.trend },
            },
            scales: { y: { beginAtZero: true } },
          },
        });
      },
      renderDeviceChart: function () {
        var canvas = document.getElementById("deviceChart");
        this.destroyChart("deviceChart", "devices");
        if (!canvas || !this.deviceStats.length || typeof Chart === "undefined") {
          return;
        }
        this.charts.devices = new Chart(canvas.getContext("2d"), {
          type: "doughnut",
          data: {
            labels: this.deviceStats.map(function (d) {
              return d.device_type;
            }),
            datasets: [
              {
                data: this.deviceStats.map(function (d) {
                  return d.sessions;
                }),
                backgroundColor: ["#4CAF50", "#2196F3", "#FF9800", "#9C27B0"],
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true, position: "bottom" },
              title: { display: true, text: this.i18n.devices },
            },
          },
        });
      },
      renderPopularPagesChart: function () {
        var canvas = document.getElementById("popularPagesChart");
        this.destroyChart("popularPagesChart", "pages");
        if (!canvas || !this.popularPages.length || typeof Chart === "undefined") {
          return;
        }
        var top = this.popularPages.slice(0, 10);
        this.charts.pages = new Chart(canvas.getContext("2d"), {
          type: "bar",
          data: {
            labels: top.map(function (p) {
              return p.page_name;
            }),
            datasets: [
              {
                label: this.i18n.sessions,
                data: top.map(function (p) {
                  return p.visits;
                }),
                backgroundColor: "#2196F3",
              },
            ],
          },
          options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false },
              title: { display: true, text: this.i18n.topPages },
            },
            scales: { x: { beginAtZero: true } },
          },
        });
      },
      applyDateFilter: function () {
        this.loadAllData();
      },
      exportData: function () {
        var self = this;
        self.exporting = true;
        fetch(self.apiUrl("export?" + self.buildParams()), {
          credentials: "same-origin",
          headers: { "X-Requested-With": "XMLHttpRequest" },
        })
          .then(function (response) {
            if (response.status === 401) {
              if (typeof M !== "undefined") {
                M.toast({ html: self.i18n.unauthorized });
              }
              return null;
            }
            var type = response.headers.get("content-type") || "";
            if (type.indexOf("text/csv") === -1) {
              return null;
            }
            return response.blob();
          })
          .then(function (blob) {
            if (!blob) {
              return;
            }
            var url = window.URL.createObjectURL(blob);
            var link = document.createElement("a");
            link.href = url;
            link.download = "analytics_export_" + self.dateRange.end + ".csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
          })
          .catch(function () {
            if (typeof M !== "undefined") {
              M.toast({ html: "Ocurrió un error inesperado" });
            }
          })
          .then(function () {
            self.exporting = false;
          });
      },
      setupRealtimeRefresh: function () {
        var self = this;
        self.realtimeInterval = setInterval(function () {
          self.loadRealtimeVisitors();
        }, 30000);
      },
      formatNumber: function (num) {
        return num ? Number(num).toLocaleString() : "0";
      },
    },
    mounted: function () {
      var self = this;
      this.$nextTick(function () {
        self.loadAllData();
        self.setupRealtimeRefresh();
      });
    },
    beforeDestroy: function () {
      if (this.realtimeInterval) {
        clearInterval(this.realtimeInterval);
      }
      Object.keys(this.charts).forEach(function (key) {
        if (this.charts[key]) {
          this.charts[key].destroy();
        }
      }, this);
    },
  });
}
