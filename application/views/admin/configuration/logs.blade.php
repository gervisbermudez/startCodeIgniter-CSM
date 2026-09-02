@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <div class="col s12 config-content">
            <div class="config-logs-toolbar">
                <div class="config-section-header">
                    <h2 class="page-header">{{ lang('menu_logs') }}</h2>
                    <p class="section-description">@{{ sourceLede }}</p>
                </div>
                <div class="config-section-nav">
                    <div class="config-section-tabs" role="tablist" aria-label="{{ lang('menu_logs') }}">
                        <button type="button" class="status-chip" :class="{active: activeTab == 'system'}" @click="changeTab('system')" role="tab" :aria-selected="activeTab == 'system' ? 'true' : 'false'">{{ lang('config_logs_system') }}</button>
                        <button type="button" class="status-chip" :class="{active: activeTab == 'api'}" @click="changeTab('api')" role="tab" :aria-selected="activeTab == 'api' ? 'true' : 'false'">{{ lang('config_logs_api') }}</button>
                        <button type="button" class="status-chip" :class="{active: activeTab == 'tracking'}" @click="changeTab('tracking')" role="tab" :aria-selected="activeTab == 'tracking' ? 'true' : 'false'">{{ lang('config_logs_tracking') }}</button>
                    </div>
                </div>
            </div>

            <div class="config-logs-split">
                <div class="config-logs-main">
                    <data-table
                        :key="activeTab"
                        :endpoint="endpoint"
                        :colums="colums"
                        :index_data="index_data"
                        :pagination="true"
                        :show_fab="false"
                        :empty_title="emptyTitle"
                    ></data-table>
                </div>

                <aside class="config-logs-aside card z-depth-1">
                    <div class="card-content">
                        <button type="button" class="config-logs-aside__head" @click="toggleInsights" :aria-expanded="insightsOpen ? 'true' : 'false'">
                            <span>{{ lang('config_logs_overview') }}</span>
                            <i class="material-icons" aria-hidden="true">@{{ insightsOpen ? 'expand_less' : 'expand_more' }}</i>
                        </button>

                        <div v-show="insightsOpen">
                            <div class="config-logs-kpis-mini" role="group" aria-label="{{ lang('config_logs_kpi_all') }}">
                                <div>
                                    <span>{{ lang('config_logs_kpi_all') }}</span>
                                    <strong>@{{ formatCount(summary.total) }}</strong>
                                </div>
                                <div>
                                    <span>{{ lang('config_logs_kpi_week') }}</span>
                                    <strong>@{{ formatCount(summary.last_7) }}</strong>
                                </div>
                                <div>
                                    <span>{{ lang('config_logs_kpi_today') }}</span>
                                    <strong>@{{ formatCount(summary.today) }}</strong>
                                </div>
                            </div>

                            <div class="config-section-tabs config-section-tabs--compact" role="tablist" aria-label="{{ lang('config_logs_overview') }}">
                                <button type="button" class="status-chip" :class="{active: chartPanel == 'activity'}" @click="setChartPanel('activity')" role="tab" :aria-selected="chartPanel == 'activity' ? 'true' : 'false'">{{ lang('config_logs_chart_activity') }}</button>
                                <button type="button" class="status-chip" :class="{active: chartPanel == 'mix'}" @click="setChartPanel('mix')" role="tab" :aria-selected="chartPanel == 'mix' ? 'true' : 'false'">{{ lang('config_logs_chart_mix') }}</button>
                            </div>

                            <p class="config-help">@{{ chartHelp }}</p>

                            <div class="config-chart-wrap" v-show="chartPanel == 'activity' && hasTrend">
                                <canvas id="logsTrendChart" :aria-label="chartHelp"></canvas>
                            </div>
                            <p class="config-help" v-if="chartPanel == 'activity' && !summaryLoading && !hasTrend">{{ lang('config_logs_no_data') }}</p>

                            <div class="config-chart-donut" v-show="chartPanel == 'mix' && hasBreakdown">
                                <div class="config-chart-wrap config-chart-wrap--donut">
                                    <canvas id="logsBreakdownChart" :aria-label="chartHelp"></canvas>
                                </div>
                                <ul class="config-chart-legend">
                                    <li v-for="row in breakdownLegend" :key="row.label">
                                        <span class="config-chart-legend__swatch" :style="{ backgroundColor: row.color }"></span>
                                        <span class="config-chart-legend__label">@{{ row.label }}</span>
                                        <span class="config-chart-legend__value">@{{ formatCount(row.total) }}</span>
                                    </li>
                                </ul>
                            </div>
                            <p class="config-help" v-if="chartPanel == 'mix' && !summaryLoading && !hasBreakdown">{{ lang('config_logs_no_data') }}</p>

                            <ul class="config-logs-toplist" v-if="chartPanel == 'mix' && activeTab == 'tracking' && summary.top_pages && summary.top_pages.length">
                                <li class="config-logs-toplist__head">{{ lang('config_logs_top_pages') }}</li>
                                <li v-for="row in summary.top_pages" :key="row.label">
                                    <span class="config-logs-toplist__label">@{{ row.label }}</span>
                                    <strong>@{{ formatCount(row.total) }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.configuration.components.i18n')
@include('admin.components.data_table_component')
@include('admin.components.data_edit_component')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/DataEditComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/LogsDataComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
