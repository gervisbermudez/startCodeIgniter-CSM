@extends('admin.layouts.app')
@section('title', lang('analytics_dashboard'))
@section('header')
<style>
  .analytics-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    position: relative;
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

  .analytics-empty {
    text-align: center;
    padding: 40px 16px;
    color: #757575;
  }
</style>
@endsection

@section('content')
<div id="analytics-dashboard"
     data-page-id="{{ isset($page_id) ? $page_id : '' }}"
     data-i18n-sessions="{{ lang('analytics_sessions') }}"
     data-i18n-pageviews="{{ lang('analytics_pageviews') }}"
     data-i18n-trend="{{ lang('analytics_traffic_trend') }}"
     data-i18n-devices="{{ lang('analytics_visits_by_device') }}"
     data-i18n-top-pages="{{ lang('analytics_top_pages') }}"
     data-i18n-no-data="{{ lang('analytics_no_data') }}"
     data-i18n-unauthorized="{{ lang('analytics_unauthorized') }}">
  <div class="date-filter row">
    <div class="col s12 m3">
      <label>{{ lang('analytics_start_date') }}</label>
      <input type="date" v-model="dateRange.start" class="browser-default">
    </div>
    <div class="col s12 m3">
      <label>{{ lang('analytics_end_date') }}</label>
      <input type="date" v-model="dateRange.end" class="browser-default">
    </div>
    <div class="col s12 m3">
      <label>&nbsp;</label><br>
      <button type="button" @click="applyDateFilter" class="btn blue waves-effect waves-light">
        <i class="material-icons left">filter_list</i>
        {{ lang('analytics_apply_filter') }}
      </button>
    </div>
    <div class="col s12 m3">
      <label>&nbsp;</label><br>
      <button type="button" @click="exportData" class="btn green waves-effect waves-light" :disabled="exporting">
        <i class="material-icons left">file_download</i>
        {{ lang('analytics_export_csv') }}
      </button>
    </div>
  </div>

  <div v-if="pageId" class="row">
    <div class="col s12">
      <div class="chip">
        {{ lang('analytics_filtering_page') }}: @{{ pageId }}
        <a :href="clearPageFilterUrl" style="margin-left: 8px;">{{ lang('analytics_clear_filter') }}</a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12 m6 l3">
      <div class="metric-card blue">
        <div class="metric-label">{{ lang('analytics_total_sessions') }}</div>
        <div class="metric-value">@{{ formatNumber(overview.total_sessions) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card green">
        <div class="metric-label">{{ lang('analytics_unique_visitors') }}</div>
        <div class="metric-value">@{{ formatNumber(overview.unique_visitors) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card orange">
        <div class="metric-label">{{ lang('analytics_total_pageviews') }}</div>
        <div class="metric-value">@{{ formatNumber(overview.total_pageviews) }}</div>
      </div>
    </div>
    <div class="col s12 m6 l3">
      <div class="metric-card purple">
        <div class="metric-label">{{ lang('analytics_avg_time_on_page') }}</div>
        <div class="metric-value" style="font-size: 2rem;">@{{ formattedAvgTime }}</div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_bounce_rate') }}</strong></h6>
        <div style="font-size: 2rem; color: #FF5722;">@{{ overview.bounce_rate }}%</div>
      </div>
    </div>
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_conversion_rate') }}</strong></h6>
        <div style="font-size: 2rem; color: #4CAF50;">@{{ overview.conversion_rate }}%</div>
      </div>
    </div>
    <div class="col s12 m4">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_pages_per_session') }}</strong></h6>
        <div style="font-size: 2rem; color: #2196F3;">@{{ overview.pages_per_session }}</div>
      </div>
    </div>
  </div>

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
        <h6><strong>{{ lang('analytics_traffic_trend') }}</strong></h6>
        <div v-if="!loading.trend && trendData.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-show="trendData.length > 0" class="chart-container large">
          <canvas id="trendChart"></canvas>
        </div>
      </div>
    </div>
  </div>

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
        <h6><strong>{{ lang('analytics_visits_by_device') }}</strong></h6>
        <div v-if="!loading.devices && deviceStats.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-show="deviceStats.length > 0" class="chart-container">
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
        <h6><strong>{{ lang('analytics_top_pages') }}</strong></h6>
        <div v-if="!loading.pages && popularPages.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-show="popularPages.length > 0" class="chart-container">
          <canvas id="popularPagesChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12 m6">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_popular_pages') }}</strong></h6>
        <div v-if="popularPages.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="page-table striped">
            <thead>
              <tr>
                <th>{{ lang('analytics_page') }}</th>
                <th>{{ lang('analytics_visits') }}</th>
                <th>{{ lang('analytics_avg_time') }}</th>
                <th>{{ lang('analytics_bounce_rate') }}</th>
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

    <div class="col s12 m6">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_traffic_sources') }}</strong></h6>
        <div v-if="trafficSources.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="striped">
            <thead>
              <tr>
                <th>{{ lang('analytics_source') }}</th>
                <th>{{ lang('analytics_type') }}</th>
                <th>{{ lang('analytics_sessions') }}</th>
                <th>{{ lang('analytics_conv_rate') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="source in trafficSources.slice(0, 10)" :key="source.referer_page">
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                  @{{ source.referer_page }}
                </td>
                <td>
                  <span class="chip" :class="(source.source_type || '').toLowerCase()">
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

  <div class="row">
    <div class="col s12">
      <div class="analytics-card">
        <h6><strong>{{ lang('analytics_top_events') }}</strong></h6>
        <div v-if="topEvents.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_data') }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="striped">
            <thead>
              <tr>
                <th>{{ lang('analytics_event_category') }}</th>
                <th>{{ lang('analytics_event_action') }}</th>
                <th>{{ lang('analytics_event_count') }}</th>
                <th>{{ lang('analytics_sessions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in topEvents" :key="event.event_category + '-' + event.event_action">
                <td>@{{ event.event_category }}</td>
                <td>@{{ event.event_action }}</td>
                <td>@{{ formatNumber(event.total) }}</td>
                <td>@{{ formatNumber(event.sessions) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
      <div class="analytics-card">
        <h6>
          <span class="realtime-indicator"></span>
          <strong>{{ lang('analytics_realtime') }}</strong>
        </h6>
        <div v-if="realtimeVisitors.length === 0" class="analytics-empty">
          <p>{{ lang('analytics_no_realtime') }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="striped">
            <thead>
              <tr>
                <th>{{ lang('analytics_page') }}</th>
                <th>{{ lang('analytics_active_sessions') }}</th>
                <th>{{ lang('analytics_pageviews') }}</th>
                <th>{{ lang('analytics_mobile') }}</th>
                <th>{{ lang('analytics_desktop') }}</th>
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
