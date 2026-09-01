@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/dashboard.min.css')?>">
<style>
    [v-cloak] { display: none; }
    .rotating {
        animation: rotate 1s linear infinite;
    }
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .text-green {
        color: #4caf50;
    }
    .text-red {
        color: #f44336;
    }
    /* Mejorar truncamiento de texto en charts */
    .chart .col2 .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
        max-width: 100%;
    }
    .chart .chart-header .chart-description {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 50%;
    }
    
    /* Quick Actions Modal */
    #quickActionsModal {
        max-width: 600px;
        border-radius: 8px;
    }
    .quick-search-input {
        border: none;
        border-bottom: 2px solid #26a69a !important;
        font-size: 1.2rem;
        margin: 0 !important;
    }
    .quick-actions-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .quick-action-item {
        padding: 12px 20px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }
    .quick-action-item:hover {
        background: #f5f5f5;
    }
    .quick-action-item i {
        margin-right: 15px;
        vertical-align: middle;
    }
    .quick-action-shortcut {
        float: right;
        color: #999;
        font-size: 0.85rem;
    }
    
    .kpi-cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin: 20px;
    }
    @media only screen and (max-width: 1200px) {
        .kpi-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media only screen and (max-width: 600px) {
        .kpi-cards {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    a.kpi-card,
    .kpi-card-link {
        display: block;
        min-width: 0;
        color: inherit;
        text-decoration: none;
        cursor: pointer;
        box-sizing: border-box;
    }
    .overview-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px;
        gap: 12px;
    }
    .overview-row .overview {
        margin: 0;
    }
    .panel-title-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }
    .panel-title-row h5 {
        margin: 0;
    }
    .dashboard-empty {
        text-align: center;
        padding: 28px 16px;
        color: #757575;
    }
    .dashboard-empty i.material-icons {
        font-size: 36px;
        display: block;
        margin-bottom: 8px;
        color: #9e9e9e;
    }
    .charts-grid {
        display: contents;
    }
    .tracking-notice {
        margin: 0 20px 16px;
        padding: 12px 16px;
        background: #fff8e1;
        border-radius: 8px;
        color: #5D5D5D;
    }

    .kpi-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .kpi-icon {
        font-size: 2.5rem;
        color: #26a69a;
        margin-bottom: 10px;
    }
    .kpi-value {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
    }
    .kpi-label {
        color: #666;
        font-size: 0.9rem;
        margin-top: 5px;
    }
    .kpi-change {
        font-size: 0.85rem;
        margin-top: 8px;
    }
    .kpi-change.positive {
        color: #4caf50;
    }
    .kpi-change.negative {
        color: #f44336;
    }
    
    /* Dark Mode Toggle */
    .theme-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    
    /* Dark Mode Styles */
    html.dark-mode {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }
    html.dark-mode .dashboard {
        background-color: #1a1a1a;
    }
    html.dark-mode .panel,
    html.dark-mode .kpi-card,
    html.dark-mode .chart,
    html.dark-mode .welcome,
    html.dark-mode .creator-container {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
        color: #e0e0e0;
    }
    html.dark-mode .collection {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
    }
    html.dark-mode .collection-item {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
        color: #e0e0e0;
    }
    html.dark-mode h5,
    html.dark-mode .kpi-value,
    html.dark-mode .chart-header {
        color: #e0e0e0;
    }
</style>
@endsection

@section('content')
@php
if (!isset($dashboard_caps) || !is_array($dashboard_caps)) {
    $dashboard_caps = dashboard_capabilities();
}
if (!isset($dashboard_fab)) {
    $dashboard_fab = dashboard_primary_create($dashboard_caps);
}
@endphp
<div class="container large dashboard dashboard-layout dashboard-single" :class="{showLoader: loader, 'is-picking': pickerOpen, 'is-editing-layout': layoutEditing}" id="root" v-cloak>
    <div v-show="loader">
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-list heightForSkeleton-list">&nbsp;</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                    <div class="col s6">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-show="!loader">
        <div class="overview-row">
            <div class="overview">
                <span>{{ lang('dashboard_overview') }}</span>
            </div>
            <div v-if="layoutEditing" class="dashboard-layout-toolbar__actions">
                <a href="#!" class="btn-flat waves-effect" @click.prevent="cancelEditLayout">
                    {{ lang('dashboard_layout_done') }}
                </a>
            </div>
        </div>

        @if(has_permisions('SELECT_ANALYTICS') && config('SITEM_TRACK_VISITORS') != 'Si')
        <div class="tracking-notice">
            {{ lang('dashboard_tracking_disabled') }}
            <a href="{{ base_url('admin/configuration') }}">{{ lang('dashboard_enable_tracking') }}</a>
        </div>
        @endif

        <div v-if="layoutEditing && canEditLayout" class="dashboard-layout-toolbar">
            <div class="dashboard-layout-toolbar__actions">
                <button class="btn waves-effect waves-light" :class="{disabled: layoutSaving}" @click="saveLayout">
                    {{ lang('dashboard_layout_save') }}
                </button>
                <button type="button" class="btn-flat waves-effect" :class="{disabled: layoutSaving}" @click="saveLayoutAsDefault">
                    {{ lang('dashboard_layout_save_default') }}
                </button>
                <button class="btn-flat waves-effect" :class="{disabled: layoutSaving}" @click="resetLayout">
                    {{ lang('dashboard_layout_reset') }}
                </button>
                <button type="button" class="btn-flat waves-effect dashboard-layout-toolbar__ghost" @click="addRow">
                    <i class="material-icons">view_agenda</i>
                    <span>{{ lang('dashboard_layout_add_row') }}</span>
                </button>
            </div>
        </div>

        <div class="dashboard-widgets" :class="{'is-editing': layoutEditing}">
            <div v-for="(row, ri) in visibleRows" :key="'row-'+ri" class="dashboard-grid-row" :class="{'is-editing': layoutEditing}">
                <div class="dashboard-grid-row__frame">
                    <div v-if="layoutEditing && canEditLayout" class="dashboard-grid-row__bar">
                        <span class="dashboard-grid-row__label">{{ lang('dashboard_layout_row') }} @{{ ri + 1 }}</span>
                        <div class="dashboard-grid-row__btns">
                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="ri === 0" data-position="top" data-tooltip="{{ lang('dashboard_layout_up') }}" aria-label="{{ lang('dashboard_layout_up') }}" @click="moveRow(ri, -1)">
                                <i class="material-icons">arrow_upward</i>
                            </button>
                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="ri === visibleRows.length - 1" data-position="top" data-tooltip="{{ lang('dashboard_layout_down') }}" aria-label="{{ lang('dashboard_layout_down') }}" @click="moveRow(ri, 1)">
                                <i class="material-icons">arrow_downward</i>
                            </button>
                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="row.cols.length >= maxCols" data-position="top" data-tooltip="{{ lang('dashboard_layout_add_column') }}" aria-label="{{ lang('dashboard_layout_add_column') }}" @click="addColumn(ri)">
                                <i class="material-icons">view_column</i>
                            </button>
                            <button type="button" class="dashboard-icon-btn tooltipped" data-position="top" data-tooltip="{{ lang('dashboard_layout_remove_row') }}" aria-label="{{ lang('dashboard_layout_remove_row') }}" @click="removeRow(ri)">
                                <i class="material-icons">delete_outline</i>
                            </button>
                        </div>
                    </div>
                    <div class="row dashboard-grid-row__cols">
                        <div v-for="(col, ci) in row.cols" :key="'col-'+ri+'-'+ci" :class="colClass(col.w, layoutEditing)">
                            <div class="dashboard-grid-col" :class="{'is-editing': layoutEditing, 'is-target': pickerOpen && pickerRi === ri && pickerCi === ci}">
                                <div v-if="layoutEditing && canEditLayout" class="dashboard-grid-col__bar">
                                    <div class="dashboard-grid-col__widths" role="group" aria-label="{{ lang('dashboard_layout_width') }}">
                                        <button type="button" v-for="size in columnWidths" :key="'w-'+ri+'-'+ci+'-'+size" class="dashboard-width-chip" :class="{active: col.w === size}" @click="setColumnWidth(ri, ci, size)">@{{ size }}</button>
                                    </div>
                                    <button type="button" class="dashboard-icon-btn tooltipped" data-position="top" data-tooltip="{{ lang('dashboard_layout_remove_column') }}" aria-label="{{ lang('dashboard_layout_remove_column') }}" @click="removeColumn(ri, ci)">
                                        <i class="material-icons">close</i>
                                    </button>
                                </div>
                                <div v-for="(item, wi) in col.items" :key="item.id" class="dashboard-widget" :class="{'is-editing': layoutEditing}">
                                    <div v-if="layoutEditing && canEditLayout" class="dashboard-widget-bar">
                                        <div class="dashboard-widget-bar__title">
                                            <i class="material-icons tiny">@{{item.icon}}</i>
                                            <span>@{{widgetTitle(item)}}</span>
                                        </div>
                                        <div class="dashboard-widget-bar__btns">
                                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="ci === 0" data-position="top" data-tooltip="{{ lang('dashboard_layout_left') }}" aria-label="{{ lang('dashboard_layout_left') }}" @click="moveWidgetAcross(ri, ci, wi, -1)">
                                                <i class="material-icons">arrow_back</i>
                                            </button>
                                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="wi === 0" data-position="top" data-tooltip="{{ lang('dashboard_layout_up') }}" aria-label="{{ lang('dashboard_layout_up') }}" @click="moveWidgetInColumn(ri, ci, wi, -1)">
                                                <i class="material-icons">arrow_upward</i>
                                            </button>
                                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="wi === col.items.length - 1" data-position="top" data-tooltip="{{ lang('dashboard_layout_down') }}" aria-label="{{ lang('dashboard_layout_down') }}" @click="moveWidgetInColumn(ri, ci, wi, 1)">
                                                <i class="material-icons">arrow_downward</i>
                                            </button>
                                            <button type="button" class="dashboard-icon-btn tooltipped" :disabled="ci === row.cols.length - 1" data-position="top" data-tooltip="{{ lang('dashboard_layout_right') }}" aria-label="{{ lang('dashboard_layout_right') }}" @click="moveWidgetAcross(ri, ci, wi, 1)">
                                                <i class="material-icons">arrow_forward</i>
                                            </button>
                                            <button type="button" class="dashboard-icon-btn tooltipped" data-position="top" data-tooltip="{{ lang('dashboard_layout_remove') }}" aria-label="{{ lang('dashboard_layout_remove') }}" @click="removeWidget(ri, ci, item.id)">
                                                <i class="material-icons">close</i>
                                            </button>
                                        </div>
                                    </div>
                                    <component :is="item.component" v-bind="widgetBind(item)"></component>
                                </div>
                                <button v-if="layoutEditing && canEditLayout && addableWidgets.length" type="button" class="dashboard-col-add tooltipped" data-position="top" data-tooltip="{{ lang('dashboard_layout_add_here') }}" aria-label="{{ lang('dashboard_layout_add_here') }}" :aria-expanded="pickerOpen && pickerRi === ri && pickerCi === ci ? 'true' : 'false'" @click="openPicker(ri, ci)">
                                    <i class="material-icons">add</i>
                                </button>
                            </div>
                        </div>
                        <div v-if="layoutEditing && canEditLayout && row.cols.length < maxCols && leftoverWidth(row) > 0" :class="addColClass(row)">
                            <button type="button" class="dashboard-slot-add tooltipped" data-position="top" data-tooltip="{{ lang('dashboard_layout_add_column') }}" aria-label="{{ lang('dashboard_layout_add_column') }}" @click="addColumn(ri)">
                                <i class="material-icons">add</i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button v-if="layoutEditing && canEditLayout" type="button" class="dashboard-layout-add-row" @click="addRow">
                <i class="material-icons">add</i>
                <span>{{ lang('dashboard_layout_add_row') }}</span>
            </button>
            <div v-if="layoutIsEmpty && !layoutEditing" class="col s12">
                <div class="dashboard-empty">
                    <i class="material-icons">dashboard</i>
                    <p>{{ lang('dashboard_layout_empty') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div v-if="layoutEditing && canEditLayout && pickerOpen" class="dashboard-picker-overlay" @click="closePicker"></div>
    <aside v-if="layoutEditing && canEditLayout && pickerOpen" class="dashboard-picker" role="dialog" aria-modal="true" aria-labelledby="dashboard-picker-title">
    <div class="dashboard-picker__head">
        <h2 id="dashboard-picker-title">{{ lang('dashboard_layout_picker') }}</h2>
        <button type="button" class="dashboard-icon-btn" aria-label="{{ lang('dashboard_layout_close_picker') }}" @click="closePicker">
            <i class="material-icons">close</i>
        </button>
    </div>
    <div v-if="addableWidgets.length" class="dashboard-picker__search">
        <i class="material-icons" aria-hidden="true">search</i>
        <input
            type="search"
            class="dashboard-picker__search-input"
            v-model="pickerQuery"
            placeholder="{{ lang('dashboard_layout_picker_search') }}"
            aria-label="{{ lang('dashboard_layout_picker_search') }}"
            autocomplete="off"
        >
    </div>
    <div v-if="addableWidgets.length && pickerChipCats.length > 1" class="dashboard-picker__chips" role="tablist" aria-label="{{ lang('dashboard_widget_cat_all') }}">
        <button type="button" class="dashboard-picker-chip" :class="{ active: !pickerCategory }" @click="pickerCategory = ''">
            {{ lang('dashboard_widget_cat_all') }}
        </button>
        <button
            type="button"
            class="dashboard-picker-chip"
            v-for="cat in pickerChipCats"
            :key="'chip-'+cat"
            :class="{ active: pickerCategory === cat }"
            @click="setPickerCategory(cat)"
        >
            @{{ categoryTitle(cat) }}
        </button>
    </div>
    <p v-if="addableWidgets.length && filteredAddableWidgets.length" class="dashboard-picker__hint">{{ lang('dashboard_layout_picker_hint') }}</p>
    <div v-if="!addableWidgets.length" class="dashboard-empty">
        <i class="material-icons">check</i>
        <p>{{ lang('dashboard_layout_picker_empty') }}</p>
    </div>
    <div v-else-if="!filteredAddableWidgets.length" class="dashboard-empty">
        <i class="material-icons">search</i>
        <p>{{ lang('dashboard_layout_picker_no_match') }}</p>
        <button type="button" class="btn-flat waves-effect teal-text" @click="pickerQuery = ''; pickerCategory = ''">{{ lang('filter_empty_cta') }}</button>
    </div>
    <div class="dashboard-picker__list">
        <section class="dashboard-picker-group" v-for="group in pickerGroups" :key="'grp-'+group.id">
            <h3 class="dashboard-picker-group__title">@{{ group.title }}</h3>
            <button type="button" class="dashboard-picker-card" v-for="w in group.widgets" :key="'pick-'+w.id" @click="pickWidget(w.id)">
                <span class="dashboard-picker-card__title">
                    <i class="material-icons tiny">@{{ w.icon }}</i>
                    @{{ widgetTitle(w) }}
                </span>
                <div class="dashboard-picker-card__preview">
                    <dashboard-widget-preview :widget-id="w.id"></dashboard-widget-preview>
                </div>
            </button>
        </section>
    </div>
    </aside>
</div>
@if(!empty($dashboard_fab))
<div class="fixed-action-btn">
    <a data-position="left" data-delay="50" data-tooltip="{{ $dashboard_fab['tip'] }}"
        class="btn-floating btn-large tooltipped st-accent" href="{{ $dashboard_fab['url'] }}">
        <i class="large material-icons">{{ $dashboard_fab['icon'] }}</i>
    </a>
    <ul>
        @if(has_permisions('CREATE_PAGE'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_page') }}"
                class="btn-floating tooltipped st-accent" href="{{base_url('admin/pages/new/')}}"><i
                    class="material-icons">web</i></a></li>
        @endif
        @if(has_permisions('CREATE_USER'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_user') }}" class="btn-floating tooltipped"
                href="{{base_url('admin/users/add')}}"><i class="material-icons">perm_identity</i></a></li>
        @endif
        @if(has_permisions('CREATE_FORM_CUSTOM'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_collection') }}" class="btn-floating tooltipped"
                href="{{base_url('admin/custommodels/new')}}"><i class="material-icons">view_module</i></a></li>
        @endif
        @if(has_permisions('CREATE_GALLERY'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_album') }}" class="btn-floating tooltipped"
                href="{{base_url('admin/gallery/new/')}}"><i class="material-icons">publish</i></a></li>
        @endif
        @if(has_permisions('CREATE_CATEGORIE'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_category') }}" class="btn-floating tooltipped"
                href="{{base_url('admin/categories/new/')}}"><i class="material-icons">receipt</i></a></li>
        @endif
        @if(has_permisions('CREATE_FRAGMENT'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_fragment') }}" class="btn-floating tooltipped"
                href="{{base_url('admin/fragments/new/')}}"><i class="material-icons">bookmark_border</i></a></li>
        @endif
        @if(has_permisions('CREATE_EVENT'))
        <li><a data-position="left" data-delay="50" data-tooltip="{{ lang('tooltip_new_event') }}" class="btn-floating tooltipped"
                href="{{ base_url('admin/events/add/') }}"><i class="material-icons">event</i></a></li>
        @endif
    </ul>
</div>
@endif
@include('admin.components.page_card_component')
@include('admin.components.users_collection_component')
@include('admin.components.create_contents_component')
@include('admin.components.file_explorer_collection_component')
@include('admin.components.albums_widget_component')

<script type="text/x-template" id="dashboard-kpis-template">
        <div class="kpi-cards">
            <a class="kpi-card kpi-card-link" :href="analyticsUrl">
                <i class="material-icons kpi-icon">people</i>
                <div class="kpi-value">@{{kpis.uniqueVisitors}}</div>
                <div class="kpi-label">{{ lang('dashboard_unique_visitors') }}</div>
                <div class="kpi-change" :class="{positive: kpis.dailyGrowth >= 0, negative: kpis.dailyGrowth < 0}">
                    <i class="material-icons tiny">@{{kpis.dailyGrowth >= 0 ? 'trending_up' : 'trending_down'}}</i>
                    @{{Math.abs(kpis.dailyGrowth)}}% {{ lang('dashboard_vs_yesterday') }}
                </div>
            </a>
            <a class="kpi-card kpi-card-link" :href="analyticsUrl">
                <i class="material-icons kpi-icon">visibility</i>
                <div class="kpi-value">@{{kpis.todayVisits}}</div>
                <div class="kpi-label">{{ lang('dashboard_today_visits') }}</div>
                <div class="kpi-change">
                    {{ lang('dashboard_yesterday') }}: @{{kpis.yesterdayVisits}}
                </div>
            </a>
            <a class="kpi-card kpi-card-link" :href="analyticsUrl">
                <i class="material-icons kpi-icon">pages</i>
                <div class="kpi-value">@{{kpis.pagesPerSession}}</div>
                <div class="kpi-label">{{ lang('dashboard_pages_per_session') }}</div>
                <div class="kpi-change">
                    {{ lang('dashboard_engagement') }}
                </div>
            </a>
            <a class="kpi-card kpi-card-link" :href="analyticsUrl">
                <i class="material-icons kpi-icon">exit_to_app</i>
                <div class="kpi-value">@{{kpis.bounceRate}}%</div>
                <div class="kpi-label">{{ lang('dashboard_bounce_rate') }}</div>
                <div class="kpi-change" :class="{positive: kpis.bounceRate < 50, negative: kpis.bounceRate >= 50}">
                    <span v-if="kpis.bounceRate < 50">{{ lang('dashboard_bounce_good') }}</span>
                    <span v-else>{{ lang('dashboard_bounce_improve') }}</span>
                </div>
            </a>
        </div>
</script>

<script type="text/x-template" id="dashboard-welcome-template">
        <div class="welcome">
            <div class="welcome_container">
                <div class="welcome_message">
                    <span class="welcome_big">{{ lang('dashboard_welcome_back') }}</span> <br />
                    <span>{{userdata('nombre') }} {{userdata('apellido') }}</span>
                </div>
                <div class="columns">
                    @if(has_permisions('SELECT_USERS'))
                    <a href="{{ base_url('admin/users') }}" class="colum st-teal" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">people</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{counts.users}}</b></div>
                            <div class="text-st-white">{{ lang('menu_users') }}</div>
                        </div>
                    </a>
                    @endif
                    @if(has_permisions('SELECT_PAGES'))
                    <a href="{{ base_url('admin/pages') }}" class="colum st-pink" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">web</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{counts.pages}}</b></div>
                            <div class="text-st-white">{{ lang('menu_pages') }}</div>
                        </div>
                    </a>
                    @endif
                    @if(has_permisions('SELECT_FILES'))
                    <a href="{{ base_url('admin/files') }}" class="colum st-gray" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">markunread_mailbox</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{counts.files}}</b></div>
                            <div class="text-st-white">{{ lang('menu_files') }}</div>
                        </div>
                    </a>
                    @endif
                    @if(has_permisions('SELECT_EVENTS'))
                    <a href="{{ base_url('admin/events') }}" class="colum st-gray-light" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-gray">event</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-gray"><b>@{{counts.events}}</b></div>
                            <div class="text-st-gray">{{ lang('menu_events') }}</div>
                        </div>
                    </a>
                    @endif
                </div>
                <div class="img">
                    <img src="{{base_url('public/img/admin/dashboard/undraw_charts.png')}}" alt="undraw_charts">
                </div>
            </div>
        </div>
</script>

<script type="text/x-template" id="dashboard-charts-template">
        <div>
                <div class="row">
                    <div class="col s12">
                        <div class="panel">
                            <div class="title panel-title-row">
                                <h5>{{ lang('dashboard_statistics') }}</h5>
                                <a :href="analyticsUrl" class="btn-flat waves-effect teal-text">
                                    {{ lang('dashboard_view_analytics') }}
                                </a>
                            </div>
                            <div class="charts">
                                <div class="dashboard-empty" v-if="canViewAnalytics && !hasAnalyticsData && !loader">
                                    <i class="material-icons">insights</i>
                                    <p>{{ lang('dashboard_no_analytics_data') }}</p>
                                    <a :href="analyticsUrl">{{ lang('dashboard_view_analytics') }}</a>
                                </div>
                                <div class="charts-grid" v-show="hasAnalyticsData">
                                <div class="chart chart-1">
                                    <div class="chart-header">
                                        {{ lang('dashboard_visits_per_day') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart1"></canvas>
                                        </div>
                                        <div class="col2">
                                            <span class="chart-title">{{ lang('dashboard_visitors') }}</span>
                                            <div class="chart-big-number">@{{stats.totalVisitors.toLocaleString()}}</div>
                                            <div class="chart-description" :class="{'text-green': stats.visitorGrowth >= 0, 'text-red': stats.visitorGrowth < 0}">{{ lang('dashboard_growth') }} @{{stats.visitorGrowth}}%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-2">
                                    <div class="chart-header">
                                        {{ lang('dashboard_requests_count') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col2">
                                            <span class="chart-title">{{ lang('dashboard_requests') }}</span>
                                            <div class="chart-big-number">@{{stats.totalRequests.toLocaleString()}}</div>
                                            <div class="chart-description" :class="{'text-green': stats.requestGrowth >= 0, 'text-red': stats.requestGrowth < 0}">{{ lang('dashboard_growth') }} @{{stats.requestGrowth}}%</div>
                                        </div>
                                        <div class="col1 ">
                                            <canvas id="myChart2"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-3">
                                    <div class="chart-header">
                                        {{ lang('dashboard_devices') }}
                                        <div class="chart-description tooltipped"
                                            data-position="top"
                                            :data-tooltip="graphs.devices.labelMayor + ' ' + graphs.devices.porcentajeMayor + '%'">@{{graphs.devices.labelMayor}}
                                            @{{graphs.devices.porcentajeMayor}}%</div>
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart3"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-4">
                                    <div class="chart-header">
                                        {{ lang('dashboard_frequent_urls') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart4"></canvas>
                                        </div>
                                        <div class="col2">
                                            <span class="truncate chart-title tooltipped"
                                                data-position="top"
                                                :data-tooltip="graphs.urlFrecuentes.labelMayor">@{{graphs.urlFrecuentes.labelMayor}}</span>
                                            <div class="chart-big-number">
                                                @{{graphs.urlFrecuentes.valorMasAlto}}</div>
                                            <div class="chart-description">
                                                @{{graphs.urlFrecuentes.porcentajeMayor}}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col s12 m6">
                        <div class="panel">
                            <div class="title panel-title-row">
                                <h5><i class="material-icons tiny">trending_up</i> {{ lang('dashboard_top_pages') }}</h5>
                                <a :href="analyticsUrl" class="btn-flat waves-effect teal-text">{{ lang('dashboard_view_analytics') }}</a>
                            </div>
                            <ul class="collection" style="border: 0; margin: 0;">
                                <li class="collection-item" v-for="(count, url) in topPages" :key="url">
                                    <a :href="analyticsUrl" class="truncate teal-text" style="max-width: 70%; display: inline-block;">@{{url}}</a>
                                    <span class="badge">@{{count}} {{ lang('analytics_visits') }}</span>
                                </li>
                                <li v-if="Object.keys(topPages).length === 0" class="collection-item dashboard-empty">
                                    {{ lang('dashboard_no_analytics_data') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="panel">
                            <div class="title panel-title-row">
                                <h5><i class="material-icons tiny">share</i> {{ lang('dashboard_traffic_sources') }}</h5>
                                <a :href="analyticsUrl" class="btn-flat waves-effect teal-text">{{ lang('dashboard_view_analytics') }}</a>
                            </div>
                            <div class="dashboard-empty" v-if="!hasReferrers">
                                {{ lang('dashboard_no_analytics_data') }}
                            </div>
                            <div v-show="hasReferrers" style="padding: 20px;">
                                <canvas id="myChartReferrers" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
</script>

<script type="text/x-template" id="dashboard-creator-template">
        <div class="row creator">
            <div class="col s12 ">
                <div class="creator-container">
                    <div class="user-avatar">
                        <img class="circle responsive-img" src="{{userdata('avatar')}}" />
                        <span class="truncate">{{ lang('dashboard_create_something') }} {{userdata('nombre')}}</span>
                    </div>
                    <div class="creator-input-field">
                        <textarea id="creator-input" placeholder="{{ lang('dashboard_creator_placeholder') }}" class="materialize-textarea"
                            v-model="creator.content"></textarea>
                    </div>
                    <div class="creator-options">
                        <div class="options-icons">
                            <i class="material-icons tooltipped" v-for="mode in creatorModes" :key="mode"
                                :class="{'active': creator.mode == mode}" data-position="top" data-delay="500"
                                :data-tooltip="$parent.creatorModeTip(mode)" @click="$parent.setCreatorMode(mode)">@{{creator.icons[mode]}}</i>
                        </div>
                        <button class="waves-effect waves-light btn" @click="$parent.saveDraft"
                            :class="{disabled: creator.content.length < 6 || creator.saving}">
                            <span v-if="!creator.saving">{{ lang('dashboard_create') }}</span>
                            <span v-else>{{ lang('dashboard_creating') }}</span>
                            <i class="material-icons right" v-if="!creator.saving">send</i>
                            <i class="material-icons right rotating" v-else>sync</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
</script>

<script type="text/x-template" id="dashboard-drafts-template">
        <div class="row drafts">
            <div class="col s12">
                <div class="title">
                    <span>{{ lang('dashboard_latest_drafts') }}</span>
                </div>
                <div class="collection">
                    <a v-for="(draf, index) in drafts" :key="index" :href="draf.link" class="collection-item"><span
                            class="badge">{{ lang('dashboard_draft_badge') }}</span><span class="truncate">@{{draf.title}}</span></a>
                    <div v-if="!drafts.length" class="dashboard-empty">{{ lang('dashboard_no_drafts') }}</div>
                </div>
            </div>
        </div>
</script>

<script type="text/x-template" id="dashboard-timeline-template">
        <div class="row timeline">
            <div class="col s12">
                <div class="title">
                    <span>{{ lang('dashboard_timeline') }}</span>
                </div>
                <div class="timeline-container">
                    <div class="card horizontal" v-for="(card, index) in timeline" :key="index">
                        <div v-if="card.imagen_file" class="card-image"
                            :style="'background-image: url(' + card.imagen_file.file_front_path + ');'"></div>
                        <div class="card-stacked">
                            <i class="material-icons card-options">more_vert</i>
                            <div class="card-header">
                                <img class="circle responsive-img"
                                    :src="card.user && card.user.avatar ? card.user.avatar : defaultAvatar" />
                                <div class="card-info">
                                    <span class="truncate title">@{{card.title}}</span>
                                    <span class="truncate datetime">@{{card.date}}</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p>@{{card.content}}</p>
                            </div>
                            <div class="card-action">
                                <a :href="card.link">{{ lang('dashboard_view_item') }}</a>
                            </div>
                        </div>
                    </div>
                    <div v-if="!timeline.length" class="dashboard-empty">{{ lang('dashboard_no_timeline') }}</div>
                </div>
            </div>
        </div>
</script>


<script type="text/x-template" id="dashboard-kpi-card-template">
    <a class="kpi-card kpi-card-link kpi-card-single" :href="analyticsUrl">
        <template v-if="widgetId === 'kpi_visitors'">
            <i class="material-icons kpi-icon">people</i>
            <div class="kpi-value">@{{kpis.uniqueVisitors}}</div>
            <div class="kpi-label">{{ lang('dashboard_unique_visitors') }}</div>
            <div class="kpi-change" :class="{positive: kpis.dailyGrowth >= 0, negative: kpis.dailyGrowth < 0}">
                <i class="material-icons tiny">@{{kpis.dailyGrowth >= 0 ? 'trending_up' : 'trending_down'}}</i>
                @{{Math.abs(kpis.dailyGrowth)}}% {{ lang('dashboard_vs_yesterday') }}
            </div>
        </template>
        <template v-else-if="widgetId === 'kpi_visits'">
            <i class="material-icons kpi-icon">visibility</i>
            <div class="kpi-value">@{{kpis.todayVisits}}</div>
            <div class="kpi-label">{{ lang('dashboard_today_visits') }}</div>
            <div class="kpi-change">{{ lang('dashboard_yesterday') }}: @{{kpis.yesterdayVisits}}</div>
        </template>
        <template v-else-if="widgetId === 'kpi_pages'">
            <i class="material-icons kpi-icon">pages</i>
            <div class="kpi-value">@{{kpis.pagesPerSession}}</div>
            <div class="kpi-label">{{ lang('dashboard_pages_per_session') }}</div>
            <div class="kpi-change">{{ lang('dashboard_engagement') }}</div>
        </template>
        <template v-else>
            <i class="material-icons kpi-icon">exit_to_app</i>
            <div class="kpi-value">@{{kpis.bounceRate}}%</div>
            <div class="kpi-label">{{ lang('dashboard_bounce_rate') }}</div>
            <div class="kpi-change" :class="{positive: kpis.bounceRate < 50, negative: kpis.bounceRate >= 50}">
                <span v-if="kpis.bounceRate < 50">{{ lang('dashboard_bounce_good') }}</span>
                <span v-else>{{ lang('dashboard_bounce_improve') }}</span>
            </div>
        </template>
    </a>
</script>

<script type="text/x-template" id="dashboard-chart-panel-template">
    <div class="panel dashboard-chart-panel">
        <div class="title panel-title-row">
            <h5 v-if="widgetId === 'chart_visits'">{{ lang('dashboard_visits_per_day') }}</h5>
            <h5 v-else-if="widgetId === 'chart_requests'">{{ lang('dashboard_requests_count') }}</h5>
            <h5 v-else-if="widgetId === 'chart_devices'">{{ lang('dashboard_devices') }}</h5>
            <h5 v-else-if="widgetId === 'chart_urls'">{{ lang('dashboard_frequent_urls') }}</h5>
            <h5 v-else-if="widgetId === 'chart_top_pages'">{{ lang('dashboard_top_pages') }}</h5>
            <h5 v-else>{{ lang('dashboard_traffic_sources') }}</h5>
            <a :href="analyticsUrl" class="btn-flat waves-effect teal-text">{{ lang('dashboard_view_analytics') }}</a>
        </div>
        <div class="dashboard-empty" v-if="canViewAnalytics && !hasAnalyticsData && !loader">
            <i class="material-icons">insights</i>
            <p>{{ lang('dashboard_no_analytics_data') }}</p>
        </div>
        <div v-show="hasAnalyticsData || widgetId === 'chart_top_pages' || widgetId === 'chart_referrers'">
            <div v-if="widgetId === 'chart_visits'" class="chart chart-solo">
                <div class="chart-body">
                    <div class="col1"><canvas id="dashChartVisits"></canvas></div>
                    <div class="col2">
                        <span class="chart-title">{{ lang('dashboard_visitors') }}</span>
                        <div class="chart-big-number">@{{stats.totalVisitors.toLocaleString()}}</div>
                    </div>
                </div>
            </div>
            <div v-else-if="widgetId === 'chart_requests'" class="chart chart-solo">
                <div class="chart-body">
                    <div class="col1"><canvas id="dashChartRequests"></canvas></div>
                    <div class="col2">
                        <span class="chart-title">{{ lang('dashboard_requests') }}</span>
                        <div class="chart-big-number">@{{stats.totalRequests.toLocaleString()}}</div>
                    </div>
                </div>
            </div>
            <div v-else-if="widgetId === 'chart_devices'" class="chart chart-solo">
                <div class="chart-body">
                    <div class="col1"><canvas id="dashChartDevices"></canvas></div>
                </div>
            </div>
            <div v-else-if="widgetId === 'chart_urls'" class="chart chart-solo">
                <div class="chart-body">
                    <div class="col1"><canvas id="dashChartUrls"></canvas></div>
                </div>
            </div>
            <ul v-else-if="widgetId === 'chart_top_pages'" class="collection dashboard-plain-list">
                <li class="collection-item" v-for="(count, url) in topPages" :key="url">
                    <span class="truncate">@{{url}}</span>
                    <span class="badge">@{{count}} {{ lang('analytics_visits') }}</span>
                </li>
                <li v-if="Object.keys(topPages).length === 0" class="collection-item dashboard-empty">{{ lang('dashboard_no_analytics_data') }}</li>
            </ul>
            <div v-else>
                <div class="dashboard-empty" v-if="!hasReferrers">{{ lang('dashboard_no_analytics_data') }}</div>
                <div v-show="hasReferrers" class="chart chart-solo">
                    <canvas id="dashChartReferrers"></canvas>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/x-template" id="dashboard-events-template">
    <div class="panel dashboard-side-panel">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_events') }}</h5>
            <a href="{{ base_url('admin/events') }}" class="btn-flat waves-effect teal-text">{{ lang('menu_events') }}</a>
        </div>
        <ul class="collection dashboard-plain-list">
            <li class="collection-item" v-for="ev in events" :key="ev.event_id">
                <a :href="ev.link" class="truncate teal-text">@{{ ev.name }}</a>
                <span class="badge">@{{ ev.date_start }}</span>
            </li>
            <li v-if="!events.length" class="collection-item dashboard-empty">{{ lang('dashboard_no_events') }}</li>
        </ul>
    </div>
</script>

<script type="text/x-template" id="dashboard-site-status-template">
    <div class="panel dashboard-side-panel">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_site_status') }}</h5>
            <a href="{{ base_url('admin/configuration') }}" class="btn-flat waves-effect teal-text">{{ lang('dashboard_enable_tracking') }}</a>
        </div>
        <ul class="collection dashboard-plain-list">
            <li class="collection-item">@{{ site.title }}</li>
            <li class="collection-item" v-if="site.tracking">{{ lang('dashboard_tracking_on') }}</li>
            <li class="collection-item" v-else>{{ lang('dashboard_tracking_disabled') }}</li>
            <li class="collection-item" v-if="site.theme">@{{ site.theme }}</li>
        </ul>
    </div>
</script>

<script type="text/x-template" id="dashboard-quick-settings-template">
    <div class="panel dashboard-side-panel">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_quick_settings') }}</h5>
        </div>
        <div class="dashboard-quick-settings">
            <a href="{{ base_url('admin/configuration') }}?section=general">{{ lang('config_general') }}</a>
            <a href="{{ base_url('admin/configuration') }}?section=theme">{{ lang('config_appearance') }}</a>
            <a href="{{ base_url('admin/configuration') }}?section=seo">{{ lang('config_seo') }}</a>
            <a href="{{ base_url('admin/configuration/data') }}">{{ lang('config_data_backups') }}</a>
        </div>
    </div>
</script>

<script type="text/x-template" id="dashboard-widget-preview-template">
    <div class="dash-preview" :data-widget="widgetId">
        <div v-if="widgetId === 'kpis'" class="dash-preview-kpis">
            <span>128<small>{{ lang('dashboard_unique_visitors') }}</small></span>
            <span>41<small>{{ lang('dashboard_today_visits') }}</small></span>
            <span>1.8<small>{{ lang('dashboard_pages_per_session') }}</small></span>
            <span>32%<small>{{ lang('dashboard_bounce_rate') }}</small></span>
        </div>
        <div v-else-if="widgetId === 'welcome'" class="dash-preview-welcome">
            <strong>{{ lang('dashboard_welcome_back') }}</strong>
            <div class="dash-preview-pills">
                <span>12 {{ lang('menu_users') }}</span>
                <span>8 {{ lang('menu_pages') }}</span>
                <span>24 {{ lang('menu_files') }}</span>
            </div>
        </div>
        <div v-else-if="widgetId === 'charts'" class="dash-preview-charts">
            <div class="dash-preview-bars">
                <i></i><i></i><i></i><i></i><i></i>
            </div>
            <ul>
                <li><span>/home</span><em>42</em></li>
                <li><span>/blog</span><em>18</em></li>
            </ul>
        </div>
        <div v-else-if="widgetId === 'users'" class="dash-preview-list">
            <div class="dash-preview-person">
                <i class="material-icons">account_circle</i>
                <span>Alex Rivera</span>
                <small>Editor</small>
            </div>
            <div class="dash-preview-person">
                <i class="material-icons">account_circle</i>
                <span>Sam Lee</span>
                <small>Author</small>
            </div>
        </div>
        <div v-else-if="widgetId === 'files'" class="dash-preview-list">
            <div class="dash-preview-file"><i class="material-icons">insert_drive_file</i><span>hero.jpg</span></div>
            <div class="dash-preview-file"><i class="material-icons">insert_drive_file</i><span>brief.pdf</span></div>
            <div class="dash-preview-file"><i class="material-icons">insert_drive_file</i><span>logo.svg</span></div>
        </div>
        <div v-else-if="widgetId === 'albums'" class="dash-preview-albums">
            <span>Spring</span>
            <span>Team</span>
        </div>
        <div v-else-if="widgetId === 'collections'" class="dash-preview-list">
            <div>Team</div>
            <div>FAQ</div>
        </div>
        <div v-else-if="widgetId === 'creator'" class="dash-preview-creator">
            <span class="dash-preview-input">{{ lang('dashboard_creator_placeholder') }}</span>
        </div>
        <div v-else-if="widgetId === 'drafts'" class="dash-preview-list">
            <div>{{ lang('dashboard_draft_badge') }} Landing</div>
            <div>{{ lang('dashboard_draft_badge') }} About</div>
        </div>
        <div v-else-if="widgetId === 'timeline'" class="dash-preview-list">
            <div>{{ lang('dashboard_timeline') }}</div>
        </div>
        <div v-else-if="widgetId && widgetId.indexOf('kpi_') === 0" class="dash-preview-kpis">
            <span>128<small>KPI</small></span>
        </div>
        <div v-else-if="widgetId && widgetId.indexOf('chart_') === 0" class="dash-preview-charts">
            <div class="dash-preview-bars"><i></i><i></i><i></i><i></i><i></i></div>
        </div>
        <div v-else-if="widgetId === 'events'" class="dash-preview-list">
            <div>Launch night</div>
            <div>Office hours</div>
        </div>
        <div v-else-if="widgetId === 'site_status'" class="dash-preview-list">
            <div>Start CMS</div>
            <div>{{ lang('dashboard_tracking_on') }}</div>
        </div>
        <div v-else-if="widgetId === 'quick_settings'" class="dash-preview-pills">
            <span>{{ lang('config_general') }}</span>
            <span>{{ lang('config_appearance') }}</span>
        </div>
        <div v-else-if="widgetId === 'calendar'" class="dash-preview-cal">
            <span></span><span></span><span class="is-on"></span><span></span><span></span><span class="is-on"></span><span></span>
            <span class="is-on"></span><span></span><span></span><span></span><span class="is-on"></span><span></span><span></span>
        </div>
        <div v-else-if="widgetId === 'fragments'" class="dash-preview-list">
            <div>about_me</div>
            <div>hero</div>
        </div>
        <div v-else-if="widgetId === 'inbox'" class="dash-preview-list">
            <div>Contact</div>
            <div>Newsletter</div>
        </div>
    </div>
</script>

<script type="text/x-template" id="dashboard-calendar-template">
    <div class="panel dashboard-list-panel dashboard-calendar">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_calendar') }}</h5>
            <a href="{{ base_url('admin/calendar') }}" class="btn-flat waves-effect teal-text">{{ lang('menu_calendar') }}</a>
        </div>
        <div class="dash-cal">
            <div class="dash-cal__nav">
                <button type="button" class="dashboard-icon-btn" aria-label="{{ lang('dashboard_cal_prev') }}" @click="shiftMonth(-1)">
                    <i class="material-icons">chevron_left</i>
                </button>
                <strong>@{{ monthLabel }}</strong>
                <button type="button" class="dashboard-icon-btn" aria-label="{{ lang('dashboard_cal_next') }}" @click="shiftMonth(1)">
                    <i class="material-icons">chevron_right</i>
                </button>
            </div>
            <div class="dash-cal__dow">
                <span v-for="(d, i) in weekdays" :key="'dow-'+i">@{{ d }}</span>
            </div>
            <div class="dash-cal__grid">
                <button
                    type="button"
                    class="dash-cal__day"
                    v-for="cell in cells"
                    :key="cell.key + '-' + cell.pad"
                    :class="{ mute: !cell.inMonth, today: cell.isToday, selected: cell.inMonth && cell.key === selectedKey, dotted: cell.hasEvents }"
                    :disabled="!cell.inMonth"
                    @click="selectDay(cell)"
                >
                    <span>@{{ cell.day }}</span>
                </button>
            </div>
            <ul class="collection dashboard-plain-list">
                <li class="collection-item" v-for="ev in selectedEvents" :key="ev.event_id">
                    <a :href="ev.link" class="truncate teal-text">@{{ ev.name }}</a>
                    <span class="badge">@{{ ev.date_start }}</span>
                </li>
                <li v-if="!selectedEvents.length" class="collection-item dashboard-empty">{{ lang('dashboard_no_calendar_events') }}</li>
            </ul>
        </div>
    </div>
</script>

<script type="text/x-template" id="dashboard-fragments-template">
    <div class="panel dashboard-list-panel dashboard-side-panel">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_fragments') }}</h5>
            <a href="{{ base_url('admin/fragments') }}" class="btn-flat waves-effect teal-text">{{ lang('menu_fragments') }}</a>
        </div>
        <ul class="collection dashboard-plain-list">
            <li class="collection-item" v-for="item in fragments" :key="item.fragment_id">
                <a :href="item.link" class="truncate teal-text">@{{ item.name }}</a>
                <span class="badge">@{{ item.type }}</span>
            </li>
            <li v-if="!fragments.length" class="collection-item dashboard-empty">
                <p>{{ lang('dashboard_no_fragments') }}</p>
                @if(has_permisions('CREATE_FRAGMENT'))
                <a href="{{ base_url('admin/fragments/new') }}" class="btn-small waves-effect waves-light">{{ lang('dashboard_fragments_empty_cta') }}</a>
                @endif
            </li>
        </ul>
    </div>
</script>

<script type="text/x-template" id="dashboard-inbox-template">
    <div class="panel dashboard-list-panel dashboard-side-panel">
        <div class="title panel-title-row">
            <h5>{{ lang('dashboard_widget_inbox') }}</h5>
            <a href="{{ base_url('admin/siteforms/submit') }}" class="btn-flat waves-effect teal-text">{{ lang('menu_siteforms_submissions') }}</a>
        </div>
        <ul class="collection dashboard-plain-list">
            <li class="collection-item" v-for="item in inbox" :key="item.siteform_submit_id">
                <a :href="item.link" class="truncate teal-text">@{{ item.preview || item.form_name }}</a>
                <span class="badge">@{{ item.form_name }}</span>
            </li>
            <li v-if="!inbox.length" class="collection-item dashboard-empty">
                <p>{{ lang('dashboard_no_inbox') }}</p>
                <a href="{{ base_url('admin/siteforms') }}" class="btn-small waves-effect waves-light">{{ lang('dashboard_inbox_empty_cta') }}</a>
            </li>
        </ul>
    </div>
</script>

@endsection

@section('footer_includes')
<script>
(function () {
    var extra = <?= json_encode(array(
        'dashboard_layout_saved' => lang('dashboard_layout_saved'),
        'dashboard_layout_saved_default' => lang('dashboard_layout_saved_default'),
        'dashboard_layout_forbidden' => lang('dashboard_layout_forbidden'),
        'dashboard_save_error' => lang('dashboard_save_error'),
        'dashboard_layout_up' => lang('dashboard_layout_up'),
        'dashboard_layout_down' => lang('dashboard_layout_down'),
        'dashboard_layout_remove' => lang('dashboard_layout_remove'),
        'dashboard_layout_add_row' => lang('dashboard_layout_add_row'),
        'dashboard_layout_add_column' => lang('dashboard_layout_add_column'),
        'dashboard_layout_remove_row' => lang('dashboard_layout_remove_row'),
        'dashboard_layout_remove_column' => lang('dashboard_layout_remove_column'),
        'dashboard_layout_add_here' => lang('dashboard_layout_add_here'),
        'dashboard_layout_row' => lang('dashboard_layout_row'),
        'dashboard_layout_width' => lang('dashboard_layout_width'),
        'dashboard_layout_left' => lang('dashboard_layout_left'),
        'dashboard_layout_right' => lang('dashboard_layout_right'),
        'dashboard_layout_choose' => lang('dashboard_layout_choose'),
        'dashboard_layout_picker' => lang('dashboard_layout_picker'),
        'dashboard_layout_picker_empty' => lang('dashboard_layout_picker_empty'),
        'dashboard_layout_picker_hint' => lang('dashboard_layout_picker_hint'),
        'dashboard_layout_picker_search' => lang('dashboard_layout_picker_search'),
        'dashboard_layout_picker_no_match' => lang('dashboard_layout_picker_no_match'),
        'dashboard_layout_close_picker' => lang('dashboard_layout_close_picker'),
        'dashboard_widget_cat_all' => lang('dashboard_widget_cat_all'),
        'dashboard_widget_cat_overview' => lang('dashboard_widget_cat_overview'),
        'dashboard_widget_cat_analytics' => lang('dashboard_widget_cat_analytics'),
        'dashboard_widget_cat_content' => lang('dashboard_widget_cat_content'),
        'dashboard_widget_cat_media' => lang('dashboard_widget_cat_media'),
        'dashboard_widget_cat_people' => lang('dashboard_widget_cat_people'),
        'dashboard_widget_cat_calendar' => lang('dashboard_widget_cat_calendar'),
        'dashboard_widget_cat_site' => lang('dashboard_widget_cat_site'),
        'filter_empty_cta' => lang('filter_empty_cta'),
        'dashboard_widget_kpis' => lang('dashboard_widget_kpis'),
        'dashboard_widget_welcome' => lang('dashboard_widget_welcome'),
        'dashboard_widget_charts' => lang('dashboard_widget_charts'),
        'dashboard_widget_users' => lang('dashboard_widget_users'),
        'dashboard_widget_files' => lang('dashboard_widget_files'),
        'dashboard_widget_albums' => lang('dashboard_widget_albums'),
        'dashboard_widget_collections' => lang('dashboard_widget_collections'),
        'dashboard_widget_creator' => lang('dashboard_widget_creator'),
        'dashboard_widget_drafts' => lang('dashboard_widget_drafts'),
        'dashboard_widget_timeline' => lang('dashboard_widget_timeline'),
        'dashboard_widget_kpi_visitors' => lang('dashboard_widget_kpi_visitors'),
        'dashboard_widget_kpi_visits' => lang('dashboard_widget_kpi_visits'),
        'dashboard_widget_kpi_pages' => lang('dashboard_widget_kpi_pages'),
        'dashboard_widget_kpi_bounce' => lang('dashboard_widget_kpi_bounce'),
        'dashboard_widget_chart_visits' => lang('dashboard_widget_chart_visits'),
        'dashboard_widget_chart_requests' => lang('dashboard_widget_chart_requests'),
        'dashboard_widget_chart_devices' => lang('dashboard_widget_chart_devices'),
        'dashboard_widget_chart_urls' => lang('dashboard_widget_chart_urls'),
        'dashboard_widget_chart_top_pages' => lang('dashboard_widget_chart_top_pages'),
        'dashboard_widget_chart_referrers' => lang('dashboard_widget_chart_referrers'),
        'dashboard_widget_events' => lang('dashboard_widget_events'),
        'dashboard_widget_site_status' => lang('dashboard_widget_site_status'),
        'dashboard_widget_quick_settings' => lang('dashboard_widget_quick_settings'),
        'dashboard_widget_calendar' => lang('dashboard_widget_calendar'),
        'dashboard_widget_fragments' => lang('dashboard_widget_fragments'),
        'dashboard_widget_inbox' => lang('dashboard_widget_inbox'),
        'dashboard_cal_dow' => lang('dashboard_cal_dow'),
        'dashboard_cal_prev' => lang('dashboard_cal_prev'),
        'dashboard_cal_next' => lang('dashboard_cal_next'),
        'dashboard_no_calendar_events' => lang('dashboard_no_calendar_events'),
        'dashboard_no_fragments' => lang('dashboard_no_fragments'),
        'dashboard_no_inbox' => lang('dashboard_no_inbox'),
    ), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
    window.ADMIN_LANG = Object.assign(window.ADMIN_LANG || {}, extra);
})();
window.CURRENT_USER = <?= json_encode(array(
    'user_id' => userdata('user_id'),
    'username' => userdata('username'),
    'email' => userdata('email'),
    'user_data' => array(
        'nombre' => userdata('nombre'),
        'apellido' => userdata('apellido'),
        'avatar' => userdata('avatar'),
    ),
), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>;
window.DASHBOARD_CAPS = <?= json_encode($dashboard_caps, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?=base_url('public/js/dashboard-widgets.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/DashboardModule.js?v=' . ADMIN_VERSION)?>"></script>
@endsection