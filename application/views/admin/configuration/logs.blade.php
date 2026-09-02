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

            <div class="config-logs-split" :class="{ 'is-inspecting': !!selectedEntry }">
                <div class="config-logs-aside-stack">
                <aside class="config-logs-aside card z-depth-1">
                    <div class="card-content">
                        <div class="config-logs-aside__head">
                            <span class="widget-title">{{ lang('config_logs_overview') }}</span>
                            <button
                                type="button"
                                class="btn-flat btn-small waves-effect config-logs-aside__toggle"
                                @click="toggleInsights"
                                :aria-expanded="insightsOpen ? 'true' : 'false'"
                                :aria-label="insightsOpen ? '{{ lang('config_logs_hide_charts') }}' : '{{ lang('config_logs_show_charts') }}'"
                            >
                                <i class="material-icons" aria-hidden="true">@{{ insightsOpen ? 'expand_less' : 'expand_more' }}</i>
                            </button>
                        </div>

                        <div v-show="insightsOpen">
                            <div class="config-section-tabs config-section-tabs--compact" role="tablist" aria-label="{{ lang('config_logs_chart_view') }}">
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
                        </div>
                    </div>
                </aside>

                <section class="config-logs-details card z-depth-1" v-show="!summaryLoading && summary.total">
                    <div class="card-content">
                        <span class="widget-title">{{ lang('config_logs_details') }}</span>
                        <dl class="config-logs-facts">
                            <div v-if="summary.last_at">
                                <dt>{{ lang('config_logs_last_activity') }}</dt>
                                <dd>@{{ formatWhen(summary.last_at) }}</dd>
                            </div>
                            <div v-if="summary.peak && summary.peak.total">
                                <dt>{{ lang('config_logs_busiest_day') }}</dt>
                                <dd>@{{ formatDay(summary.peak.label) }} · @{{ formatCount(summary.peak.total) }}</dd>
                            </div>
                            <div>
                                <dt>{{ lang('config_logs_avg_day') }}</dt>
                                <dd>@{{ formatCount(summary.avg_day) }}</dd>
                            </div>
                            <div v-if="activeTab == 'api'">
                                <dt>{{ lang('config_logs_rejected') }}</dt>
                                <dd>@{{ formatCount(summary.rejected) }}</dd>
                            </div>
                            <div v-if="activeTab == 'tracking'">
                                <dt>{{ lang('config_logs_unique_visitors') }}</dt>
                                <dd>@{{ formatCount(summary.unique_visitors) }}</dd>
                            </div>
                        </dl>

                        <ul class="config-logs-toplist" v-if="detailsPrimary.length">
                            <li class="config-logs-toplist__head">@{{ detailsPrimaryTitle }}</li>
                            <li v-for="row in detailsPrimary" :key="'p-' + row.label">
                                <span class="config-logs-toplist__label">@{{ row.label }}</span>
                                <strong>@{{ formatCount(row.total) }}</strong>
                            </li>
                        </ul>

                        <ul class="config-logs-toplist" v-if="detailsSecondary.length">
                            <li class="config-logs-toplist__head">@{{ detailsSecondaryTitle }}</li>
                            <li v-for="row in detailsSecondary" :key="'s-' + row.label">
                                <span class="config-logs-toplist__label">@{{ row.label }}</span>
                                <strong>@{{ formatCount(row.total) }}</strong>
                            </li>
                        </ul>
                    </div>
                </section>
                </div>

                <div class="config-logs-main">
                    <p class="config-help config-logs-select-hint">{{ lang('config_logs_select_row') }}</p>
                    <data-table
                        :key="tableKey"
                        :endpoint="endpoint"
                        :colums="colums"
                        :index_data="index_data"
                        :pagination="true"
                        :show_fab="false"
                        :empty_title="emptyTitle"
                        :query_params="listQueryParams"
                        :selectable="true"
                        :selected_id="selectedId"
                        v-on:inspect="openInspector"
                    >
                        <div slot="filters" class="config-logs-list-filters" v-if="primaryFilterChips.length || secondaryFilterChips.length">
                            <div class="filter-group" v-if="primaryFilterChips.length" role="group" :aria-label="detailsPrimaryTitle">
                                <button type="button" class="status-chip" :class="{active: !primaryFilterValue}" @click="setPrimaryFilter('')">{{ lang('config_logs_filter_all') }}</button>
                                <button
                                    type="button"
                                    class="status-chip"
                                    v-for="chip in primaryFilterChips"
                                    :key="'p-' + chip.value"
                                    :class="{active: primaryFilterValue == chip.value}"
                                    @click="setPrimaryFilter(chip.value)"
                                >@{{ chip.label }}</button>
                            </div>
                            <div class="filter-group" v-if="secondaryFilterChips.length" role="group">
                                <button
                                    type="button"
                                    class="status-chip"
                                    v-for="chip in secondaryFilterChips"
                                    :key="'s-' + chip.value"
                                    :class="{active: secondaryFilterValue == chip.value}"
                                    @click="setSecondaryFilter(chip.value)"
                                >@{{ chip.label }}</button>
                            </div>
                        </div>
                    </data-table>
                </div>

                <aside class="config-logs-inspector card z-depth-1" v-show="selectedEntry" :aria-hidden="selectedEntry ? 'false' : 'true'">
                    <div class="card-content">
                        <div class="config-logs-inspector__head">
                            <span class="widget-title">{{ lang('config_logs_entry') }}</span>
                            <button
                                type="button"
                                class="btn-flat btn-small waves-effect config-logs-aside__toggle"
                                @click="closeInspector"
                                aria-label="{{ lang('config_logs_close_entry') }}"
                            >
                                <i class="material-icons" aria-hidden="true">close</i>
                            </button>
                        </div>
                        <dl class="config-logs-inspector__fields">
                            <div v-for="row in inspectorFields" :key="row.key">
                                <dt>@{{ row.label }}</dt>
                                <dd v-if="row.kind == 'payload'"><pre>@{{ row.value }}</pre></dd>
                                <dd v-else-if="row.kind == 'link'"><a :href="row.value" target="_blank" rel="noopener">{{ lang('config_col_view') }}</a></dd>
                                <dd v-else>@{{ row.value }}</dd>
                            </div>
                        </dl>
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
