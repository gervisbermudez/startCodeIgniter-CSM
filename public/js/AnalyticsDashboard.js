/**
 * Analytics Dashboard Component
 * 
 * Comprehensive analytics dashboard with charts and metrics
 * Uses Chart.js for visualizations
 */
if (document.getElementById('analytics-dashboard') && !window.AnalyticsDashboardMounted) {
  console.log('Creating Vue instance for Analytics Dashboard');
  window.AnalyticsDashboardMounted = true;
var AnalyticsDashboard = new Vue({
  el: "#analytics-dashboard",
  data: {
    // API endpoint
    baseEndpoint: "/api/v1/analytics/",
    
    // Loading states
    loading: {
      overview: false,
      trend: false,
      pages: false,
      devices: false,
      realtime: false
    },
    
    // Data
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
      tablet_visits: 0
    },
    
    trendData: [],
    popularPages: [],
    deviceStats: [],
    trafficSources: [],
    realtimeVisitors: [],
    
    // Date filters
    dateRange: {
      start: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
      end: new Date().toISOString().split('T')[0]
    },
    
    // Charts
    charts: {
      trend: null,
      devices: null,
      pages: null,
      hourly: null
    },
    
    // Refresh interval for realtime
    realtimeInterval: null,
    
    // Export loading
    exporting: false
  },
  
  computed: {
    /**
     * Format average time on page
     */
    formattedAvgTime() {
      const seconds = Math.round(this.overview.avg_time_on_page);
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = seconds % 60;
      return `${minutes}m ${remainingSeconds}s`;
    },
    
    /**
     * Total visits (all devices)
     */
    totalVisits() {
      return this.overview.mobile_visits + 
             this.overview.desktop_visits + 
             this.overview.tablet_visits;
    }
  },
  
  methods: {
    /**
     * Initialize dashboard
     */
    async init() {
      console.log('Loading all data...');
      await this.loadAllData();
      console.log('All data loaded, setting up realtime refresh');
      this.setupRealtimeRefresh();
    },
    
    /**
     * Load all dashboard data
     */
    async loadAllData() {
      await Promise.all([
        this.loadOverview(),
        this.loadTrend(),
        this.loadPopularPages(),
        this.loadDeviceStats(),
        this.loadTrafficSources(),
        this.loadRealtimeVisitors()
      ]);
      
      console.log('Loading states:', this.loading);
    },
    
    /**
     * Load overview statistics
     */
    async loadOverview() {
      this.loading.overview = true;
      
      try {
        const params = new URLSearchParams({
          start_date: this.dateRange.start,
          end_date: this.dateRange.end
        });
        
        const url = `${this.baseEndpoint}overview?${params}`;
        console.log('Loading overview from:', url);
        const response = await fetch(url);
        const data = await response.json();
        
        console.log('Overview response:', data);
        
        if (data.code === 200 && data.data) {
          this.overview = data.data;
        }
      } catch (error) {
        console.error('Error loading overview:', error);
      } finally {
        this.loading.overview = false;
      }
    },
    
    /**
     * Load trend data and render chart
     */
    async loadTrend(days = 30) {
      this.loading.trend = true;
      
      try {
        const response = await fetch(`${this.baseEndpoint}trend?days=${days}`);
        const data = await response.json();
        
        if (data.code === 200 && data.data) {
          this.trendData = Array.isArray(data.data) ? data.data : [];
          this.renderTrendChart();
        } else {
          this.trendData = [];
        }
      } catch (error) {
        console.error('Error loading trend:', error);
      } finally {
        this.loading.trend = false;
        console.log('Trend loading set to false');
      }
    },
    
    /**
     * Load popular pages
     */
    async loadPopularPages() {
      this.loading.pages = true;
      
      try {
        const params = new URLSearchParams({
          limit: 10,
          start_date: this.dateRange.start,
          end_date: this.dateRange.end
        });
        
        const response = await fetch(`${this.baseEndpoint}popular-pages?${params}`);
        const data = await response.json();
        
        if (data.code === 200 && data.data) {
          this.popularPages = Array.isArray(data.data) ? data.data : [];
          this.renderPopularPagesChart();
        } else {
          this.popularPages = [];
        }
      } catch (error) {
        console.error('Error loading popular pages:', error);
      } finally {
        this.loading.pages = false;
        console.log('Pages loading set to false');
      }
    },
    
    /**
     * Load device statistics
     */
    async loadDeviceStats() {
      this.loading.devices = true;
      
      try {
        const params = new URLSearchParams({
          start_date: this.dateRange.start,
          end_date: this.dateRange.end
        });
        
        const response = await fetch(`${this.baseEndpoint}devices?${params}`);
        const data = await response.json();
        
        if (data.code === 200 && data.data) {
          this.deviceStats = Array.isArray(data.data) ? data.data : [];
          this.renderDeviceChart();
        } else {
          this.deviceStats = [];
        }
      } catch (error) {
        console.error('Error loading device stats:', error);
      } finally {
        this.loading.devices = false;
        console.log('Devices loading set to false');
      }
    },
    
    /**
     * Load traffic sources
     */
    async loadTrafficSources() {
      try {
        const params = new URLSearchParams({
          start_date: this.dateRange.start,
          end_date: this.dateRange.end
        });
        
        const response = await fetch(`${this.baseEndpoint}traffic-sources?${params}`);
        const data = await response.json();
        
        if (data.code === 200 && data.data) {
          this.trafficSources = Array.isArray(data.data) ? data.data : [];
        } else {
          this.trafficSources = [];
        }
      } catch (error) {
        console.error('Error loading traffic sources:', error);
      }
    },
    
    /**
     * Load realtime visitors
     */
    async loadRealtimeVisitors() {
      this.loading.realtime = true;
      
      try {
        const response = await fetch(`${this.baseEndpoint}realtime`);
        const data = await response.json();
        
        if (data.code === 200 && data.data) {
          this.realtimeVisitors = data.data;
        }
      } catch (error) {
        console.error('Error loading realtime visitors:', error);
      } finally {
        this.loading.realtime = false;
      }
    },
    
    /**
     * Render trend chart (line chart)
     */
    renderTrendChart() {
      const canvas = document.getElementById('trendChart');
      if (!canvas) return;
      
      // Destroy existing chart using Chart.js registry
      const existingChart = Chart.getChart('trendChart');
      if (existingChart) {
        existingChart.destroy();
      }
      
      if (this.charts.trend) {
        this.charts.trend = null;
      }
      
      if (!this.trendData || this.trendData.length === 0) {
        console.warn('No trend data to display');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      const labels = this.trendData.map(d => d.date);
      const sessions = this.trendData.map(d => d.sessions);
      const pageviews = this.trendData.map(d => d.pageviews);
      
      this.charts.trend = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Sessions',
              data: sessions,
              borderColor: '#4CAF50',
              backgroundColor: 'rgba(76, 175, 80, 0.1)',
              tension: 0.4
            },
            {
              label: 'Pageviews',
              data: pageviews,
              borderColor: '#2196F3',
              backgroundColor: 'rgba(33, 150, 243, 0.1)',
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            title: {
              display: true,
              text: 'Traffic Trend (Last 30 Days)'
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    },
    
    /**
     * Render device chart (doughnut chart)
     */
    renderDeviceChart() {
      const canvas = document.getElementById('deviceChart');
      if (!canvas) return;
      
      // Destroy existing chart using Chart.js registry
      const existingChart = Chart.getChart('deviceChart');
      if (existingChart) {
        existingChart.destroy();
      }
      
      if (this.charts.devices) {
        this.charts.devices = null;
      }
      
      if (!this.deviceStats || this.deviceStats.length === 0) {
        console.warn('No device stats to display');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      const labels = this.deviceStats.map(d => d.device_type);
      const data = this.deviceStats.map(d => d.sessions);
      
      this.charts.devices = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: [
              '#4CAF50',
              '#2196F3',
              '#FF9800',
              '#9C27B0'
            ]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom'
            },
            title: {
              display: true,
              text: 'Visits by Device Type'
            }
          }
        }
      });
    },
    
    /**
     * Render popular pages chart (horizontal bar)
     */
    renderPopularPagesChart() {
      const canvas = document.getElementById('popularPagesChart');
      if (!canvas) return;
      
      // Destroy existing chart using Chart.js registry
      const existingChart = Chart.getChart('popularPagesChart');
      if (existingChart) {
        existingChart.destroy();
      }
      
      if (this.charts.pages) {
        this.charts.pages = null;
      }
      
      if (!this.popularPages || this.popularPages.length === 0) {
        console.warn('No popular pages to display');
        return;
      }
      
      const ctx = canvas.getContext('2d');
      
      const labels = this.popularPages.slice(0, 10).map(p => p.page_name);
      const data = this.popularPages.slice(0, 10).map(p => p.visits);
      
      this.charts.pages = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Visits',
            data: data,
            backgroundColor: '#2196F3'
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Top 10 Most Visited Pages'
            }
          },
          scales: {
            x: {
              beginAtZero: true
            }
          }
        }
      });
    },
    
    /**
     * Apply date filter
     */
    async applyDateFilter() {
      await this.loadAllData();
    },
    
    /**
     * Export data to CSV
     */
    async exportData() {
      this.exporting = true;
      
      try {
        const params = new URLSearchParams({
          start_date: this.dateRange.start,
          end_date: this.dateRange.end
        });
        
        window.location.href = `${this.baseEndpoint}export?${params}`;
        
        setTimeout(() => {
          this.exporting = false;
        }, 2000);
      } catch (error) {
        console.error('Error exporting data:', error);
        this.exporting = false;
      }
    },
    
    /**
     * Setup realtime refresh (every 30 seconds)
     */
    setupRealtimeRefresh() {
      this.realtimeInterval = setInterval(() => {
        this.loadRealtimeVisitors();
      }, 30000); // 30 seconds
    },
    
    /**
     * Get date X days ago
     */
    getDateDaysAgo(days) {
      const date = new Date();
      date.setDate(date.getDate() - days);
      return date.toISOString().split('T')[0];
    },
    
    /**
     * Get today's date
     */
    getToday() {
      return new Date().toISOString().split('T')[0];
    },
    
    /**
     * Format number with commas
     */
    formatNumber(num) {
      return num ? num.toLocaleString() : '0';
    }
  },
  
  mounted() {
    console.log('AnalyticsDashboard mounted');
    this.$nextTick(function() {
      console.log('Initializing analytics dashboard...');
      this.init();
    });
  },
  
  beforeDestroy() {
    // Clear realtime interval
    if (this.realtimeInterval) {
      clearInterval(this.realtimeInterval);
    }
    
    // Destroy charts
    Object.values(this.charts).forEach(chart => {
      if (chart) chart.destroy();
    });
  }
});
}
