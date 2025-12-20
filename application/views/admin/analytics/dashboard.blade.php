@extends('admin.layouts.app')
@section('title', 'Analytics Dashboard')
@section('header')
<style>
  .analytics-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  
  .metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
  }
  
  .metric-card.green {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  }
  
  .metric-card.blue {
    background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
  }
  
  .metric-card.orange {
    background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  }
  
  .metric-card.purple {
    background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);
  }
  
  .metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0;
  }
  
  .metric-label {
    font-size: 0.9rem;
    opacity: 0.9;
  }
  
  .chart-container {
    position: relative;
    height: 300px;
    margin-top: 20px;
  }
  
  .chart-container.large {
    height: 400px;
  }
  
  .realtime-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: #4CAF50;
    border-radius: 50%;
    margin-right: 8px;
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
  
  .date-filter {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .table-responsive {
    max-height: 400px;
    overflow-y: auto;
  }
  
  .page-table {
    width: 100%;
  }
  
  .page-table th {
    position: sticky;
    top: 0;
    background: #f5f5f5;
    z-index: 10;
  }
  
  .loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
  }
</style>
@endsection

@section('content')
<div id="analytics-dashboard">
  <!-- Date Filter -->
  <div class="date-filter row">
    <div class="col s12 m3">
      <label>Start Date</label>
      <input type="date" v-model="dateRange.start" class="browser-default">
    </div>
    <div class="col s12 m3">
      <label>End Date</label>
      <input type="date" v-model="dateRange.end" class="browser-default">
    </div>
    <div class="col s12 m3">
      <label>&nbsp;</label><br>
      <button @click="applyDateFilter" class="btn blue waves-effect waves-light">
        <i class="material-icons left">filter_list</i>
        Apply Filter
      </button>
    </div>
    <div class="col s12 m3">
      <label>&nbsp;</label><br>
      <button @click="exportData" class="btn green waves-effect waves-light" :disabled="exporting">
        <i class="material-icons left">file_download</i>
        Export CSV
      </button>
    </div>
  </div>

  <!-- Overview Metrics -->
  <div class="row">
    <div class="col s12 m6 l3">
      <div class="metric-card blue">
        <div class="metric-label">Total Sessions</div>
        <div class="metric-value">@{{ formatNumber(overview.total_sessions) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card green">
        <div class="metric-label">Unique Visitors</div>
        <div class="metric-value">@{{ formatNumber(overview.unique_visitors) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card orange">
        <div class="metric-label">Total Pageviews</div>
        <div class="metric-value">@{{ formatNumber(overview.total_pageviews) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card purple">
        <div class="metric-label">Avg. Time on Page</div>
        <div class="metric-value" style="font-size: 2rem;">@{{ formattedAvgTime }}</div>
      </div>
    </div>
  </div>

  <!-- Secondary Metrics -->
  <div class="row">
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>Bounce Rate</strong></h6>
        <div style="font-size: 2rem; color: #FF5722;">@{{ overview.bounce_rate }}%</div>
      </div>
    </div>
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>Conversion Rate</strong></h6>
        <div style="font-size: 2rem; color: #4CAF50;">@{{ overview.conversion_rate }}%</div>
      </div>
    </div>
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>Pages per Session</strong></h6>
        <div style="font-size: 2rem; color: #2196F3;">@{{ overview.pages_per_session }}</div>
      </div>
    </div>
  </div>

  <!-- Traffic Trend Chart -->
  <div class="row">
    <div class="col s12">
      <div class="analytics-card">
        <div v-if="loading.trend" class="loading-overlay">
          <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue-only">
              <div class="circle-clipper left">
                <div class="circle"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="chart-container large">
          <canvas id="trendChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Device Stats and Popular Pages -->
  <div class="row">
    <div class="col s12 m6">
      <div class="analytics-card">
        <div v-if="loading.devices" class="loading-overlay">
          <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue-only">
              <div class="circle-clipper left">
                <div class="circle"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="deviceChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col s12 m6">
      <div class="analytics-card">
        <div v-if="loading.pages" class="loading-overlay">
          <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue-only">
              <div class="circle-clipper left">
                <div class="circle"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="popularPagesChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Popular Pages Table -->
  <div class="row">
    <div class="col s12 m6">
      <div class="analytics-card">
        <h6><strong>Popular Pages Details</strong></h6>
        <div class="table-responsive">
          <table class="page-table striped">
            <thead>
              <tr>
                <th>Page</th>
                <th>Visits</th>
                <th>Avg Time</th>
                <th>Bounce Rate</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="page in popularPages" :key="page.page_name">
                <td>@{{ page.page_name }}</td>
                <td>@{{ formatNumber(page.visits) }}</td>
                <td>@{{ Math.round(page.avg_time) }}s</td>
                <td>@{{ page.bounce_rate }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Traffic Sources -->
    <div class="col s12 m6">
      <div class="analytics-card">
        <h6><strong>Traffic Sources</strong></h6>
        <div class="table-responsive">
          <table class="striped">
            <thead>
              <tr>
                <th>Source</th>
                <th>Type</th>
                <th>Sessions</th>
                <th>Conv. Rate</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="source in trafficSources.slice(0, 10)" :key="source.referer_page">
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                  @{{ source.referer_page }}
                </td>
                <td>
                  <span class="chip" :class="source.source_type.toLowerCase()">
                    @{{ source.source_type }}
                  </span>
                </td>
                <td>@{{ formatNumber(source.sessions) }}</td>
                <td>@{{ source.conversion_rate }}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Real-time Visitors -->
  <div class="row">
    <div class="col s12">
      <div class="analytics-card">
        <h6>
          <span class="realtime-indicator"></span>
          <strong>Real-time Visitors (Last 30 minutes)</strong>
        </h6>
        <div v-if="realtimeVisitors.length === 0" class="center-align" style="padding: 20px;">
          <p>No active visitors in the last 30 minutes</p>
        </div>
        <div v-else class="table-responsive">
          <table class="striped">
            <thead>
              <tr>
                <th>Page</th>
                <th>Active Sessions</th>
                <th>Pageviews</th>
                <th>Mobile</th>
                <th>Desktop</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="visitor in realtimeVisitors" :key="visitor.page_name">
                <td>@{{ visitor.page_name }}</td>
                <td>@{{ visitor.active_sessions }}</td>
                <td>@{{ visitor.active_pageviews }}</td>
                <td>@{{ visitor.mobile }}</td>
                <td>@{{ visitor.desktop }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer_includes')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{base_url('resources/components/AnalyticsDashboard.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
