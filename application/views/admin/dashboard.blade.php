@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/dashboard.min.css')?>">
<style>
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
    
    /* KPI Cards */
    .kpi-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px;
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
    body.dark-mode {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }
    body.dark-mode .dashboard {
        background-color: #1a1a1a;
    }
    body.dark-mode .panel,
    body.dark-mode .kpi-card,
    body.dark-mode .chart,
    body.dark-mode .welcome,
    body.dark-mode .creator-container {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
        color: #e0e0e0;
    }
    body.dark-mode .collection {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
    }
    body.dark-mode .collection-item {
        background-color: #2d2d2d;
        border-color: #3d3d3d;
        color: #e0e0e0;
    }
    body.dark-mode h5,
    body.dark-mode .kpi-value,
    body.dark-mode .chart-header {
        color: #e0e0e0;
    }
</style>
@endsection

@section('content')
<div class="container large dashboard" :class="{showLoader: loader}" id="root" v-cloak>
    <div v-show="loader">
        <div class="row">
            <div class="col s8">
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-list heightForSkeleton-list">&nbsp;</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s6 ">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                    <div class="col s6">
                        <div class="skeleton-card heightForSkeleton-card"></div>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="skeleton-blog heightForSkeleton-blog"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-left" v-show="!loader">
        <div class="overview">
            <span>{{ lang('dashboard_overview') }}</span>
        </div>
        
        <!-- KPI Cards -->
        <div class="kpi-cards">
            <div class="kpi-card">
                <i class="material-icons kpi-icon">people</i>
                <div class="kpi-value">@{{kpis.uniqueVisitors}}</div>
                <div class="kpi-label">Unique Visitors</div>
                <div class="kpi-change" :class="{positive: kpis.dailyGrowth >= 0, negative: kpis.dailyGrowth < 0}">
                    <i class="material-icons tiny">@{{kpis.dailyGrowth >= 0 ? 'trending_up' : 'trending_down'}}</i>
                    @{{Math.abs(kpis.dailyGrowth)}}% vs yesterday
                </div>
            </div>
            <div class="kpi-card">
                <i class="material-icons kpi-icon">visibility</i>
                <div class="kpi-value">@{{kpis.todayVisits}}</div>
                <div class="kpi-label">Today's Visits</div>
                <div class="kpi-change">
                    Yesterday: @{{kpis.yesterdayVisits}}
                </div>
            </div>
            <div class="kpi-card">
                <i class="material-icons kpi-icon">pages</i>
                <div class="kpi-value">@{{kpis.pagesPerSession}}</div>
                <div class="kpi-label">Pages/Session</div>
                <div class="kpi-change">
                    Engagement metric
                </div>
            </div>
            <div class="kpi-card">
                <i class="material-icons kpi-icon">exit_to_app</i>
                <div class="kpi-value">@{{kpis.bounceRate}}%</div>
                <div class="kpi-label">Bounce Rate</div>
                <div class="kpi-change" :class="{positive: kpis.bounceRate < 50, negative: kpis.bounceRate >= 50}">
                    @{{kpis.bounceRate < 50 ? 'Good' : 'Needs improvement'}}
                </div>
            </div>
        </div>
        
        <div class="welcome">
            <div class="welcome_container">
                <div class="welcome_message">
                    <span class="welcome_big">{{ lang('dashboard_welcome_back') }}</span> <br />
                    <span>{{userdata('nombre') }} {{userdata('apellido') }}</span>
                </div>
                <div class="columns">
                    <a href="{{ base_url('admin/users') }}" class="colum st-teal" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">people</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{users.length}}</b></div>
                            <div class="text-st-white">{{ lang('menu_users') }}</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/pages') }}" class="colum st-pink" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">web</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{pages.length}}</b></div>
                            <div class="text-st-white">Pages</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/files') }}" class="colum st-gray" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-white">markunread_mailbox</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-white"><b>@{{files.length}}</b></div>
                            <div class="text-st-white">Files</div>
                        </div>
                    </a>
                    <a href="{{ base_url('admin/events') }}" class="colum st-gray-light" style="text-decoration:none;">
                        <div class="colum__icon">
                            <i class="material-icons text-st-gray">assistant</i>
                        </div>
                        <div class="colum__description">
                            <div class="text-st-gray"><b>@{{events.length}}</b></div>
                            <div class="text-st-gray">Events</div>
                        </div>
                    </a>
                </div>
                <div class="img">
                    <img src="{{base_url('public/img/admin/dashboard/undraw_charts.png')}}" alt="undraw_charts">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12">
                        <div class="panel">
                            <div class="title">
                                <h5>{{ lang('dashboard_statistics') }}</h5>
                            </div>
                            <div class="charts">
                                <div class="chart chart-1">
                                    <div class="chart-header">
                                        {{ lang('dashboard_visits_per_day') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col1 ">
                                            <canvas id="myChart1"></canvas>
                                        </div>
                                        <div class="col2">
                                            <span class="chart-title">VISITORS</span>
                                            <div class="chart-big-number">@{{stats.totalVisitors.toLocaleString()}}</div>
                                            <div class="chart-description" :class="{'text-green': stats.visitorGrowth >= 0, 'text-red': stats.visitorGrowth < 0}">Growth @{{stats.visitorGrowth}}%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart chart-2">
                                    <div class="chart-header">
                                        {{ lang('dashboard_requests_count') }}
                                    </div>
                                    <div class="chart-body">
                                        <div class="col2">
                                            <span class="chart-title">REQUESTS</span>
                                            <div class="chart-big-number">@{{stats.totalRequests.toLocaleString()}}</div>
                                            <div class="chart-description" :class="{'text-green': stats.requestGrowth >= 0, 'text-red': stats.requestGrowth < 0}">Growth @{{stats.requestGrowth}}%</div>
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
                                            <span class="chart-title truncate tooltipped" 
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
                
                <!-- Top Pages & Referrers -->
                <div class="row">
                    <div class="col s12 m6">
                        <div class="panel">
                            <div class="title">
                                <h5><i class="material-icons tiny">trending_up</i> Top Pages</h5>
                            </div>
                            <ul class="collection" style="border: 0; margin: 0;">
                                <li class="collection-item" v-for="(count, url) in topPages" :key="url">
                                    <span class="truncate" style="max-width: 70%; display: inline-block;">@{{url}}</span>
                                    <span class="badge">@{{count}} visits</span>
                                </li>
                                <li v-if="Object.keys(topPages).length === 0" class="collection-item center-align" style="color: #999;">
                                    No data available
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="panel">
                            <div class="title">
                                <h5><i class="material-icons tiny">share</i> Traffic Sources</h5>
                            </div>
                            <div style="padding: 20px;">
                                <canvas id="myChartReferrers" style="max-height: 200px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col m6 l6 xl4 s12">
                <users-collection :users="users"></users-collection>
            </div>
            <div class="col m6 l6 xl4 s12">
                <file-explorer-collection :files="files"></file-explorer-collection>
            </div>
            <div class="col m6 l6 xl4 s12">
                <albumes-widget :albumes="albumes"></albumes-widget>
            </div>
            <div class="col s12">
                <create-contents :forms_types="forms_types" :content="content"></create-contents>
            </div>
        </div>
    </div>
    <div class="col-right st-white" v-show="!loader">
        <div class="row creator">
            <div class="col s12 ">
                <div class="creator-container">
                    <div class="user-avatar">
                        <img class="circle responsive-img" src="{{userdata('avatar')}}" />
                        <span class="truncate">{{ lang('dashboard_create_something') }} {{userdata('nombre')}}</span>
                    </div>
                    <div class="creator-input-field">
                        <textarea id="creator-input" placeholder="Type here..." class="materialize-textarea"
                            v-model="creator.content"></textarea>
                    </div>
                    <div class="creator-options">
                        <div class="options-icons">
                            <i class="material-icons tooltipped" v-for="mode in creator.modes" :key="mode"
                                :class="{'active': creator.mode == mode}" data-position="top" data-delay="500"
                                :data-tooltip="mode" @click="setCreatorMode(mode)">@{{creator.icons[mode]}}</i>
                        </div>
                        <button class="waves-effect waves-light btn" @click="saveDraft"
                            :class="{disabled: creator.content.length < 6 || creator.saving}">
                            <span v-if="!creator.saving">Create</span>
                            <span v-else>Creating...</span>
                            <i class="material-icons right" v-if="!creator.saving">send</i>
                            <i class="material-icons right rotating" v-else>sync</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row drafts">
            <div class="col s12">
                <div class="title">
                    <span>{{ lang('dashboard_latest_drafts') }}</span>
                </div>
                <div class="collection">
                    <a v-for="(draf, index) in pages_draf" :key="index" :href="draf.link" class="collection-item"><span
                            class="badge">1</span><span class="truncate">@{{draf.title}}</span></a>
                </div>
            </div>
        </div>
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
                                    :src="card.user && card.user.avatar ? card.user.avatar : '{{base_url()}}public/img/profile/default_profile_2.jpg'" />
                                <div class="card-info">
                                    <span class="truncate title">@{{card.title}}</span>
                                    <span class="truncate datetime">@{{card.date}}</span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p>@{{card.content}}</p>
                            </div>
                            <div class="card-action">
                                <a :href="card.link">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fixed-action-btn">
    <a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_form') }}'"
        class="btn-floating btn-large tooltipped red" href="{{base_url('admin/custommodels/nuevo')}}">
        <i class="large material-icons">add</i>
    </a>
    <ul>
        @if(has_permisions('CREATE_USER'))
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_user') }}'" class="btn-floating tooltipped red"
                href="{{base_url('admin/users/agregar')}}"><i class="material-icons">perm_identity</i></a></li>
        @endif
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_page') }}'"
                class="btn-floating tooltipped yellow darken-1" href="{{base_url('admin/pages/nueva/')}}"><i
                    class="material-icons">web</i></a></li>
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_album') }}'" class="btn-floating tooltipped green"
                href="{{base_url('admin/gallery/nuevo/')}}"><i class="material-icons">publish</i></a></li>
        <li><a data-position="left" data-delay="50" :data-tooltip="'{{ lang('tooltip_new_event') }}'" class="btn-floating tooltipped blue"
                href="{{ base_url('admin/events/agregar/') }}"><i class="material-icons">assistant</i></a></li>
    </ul>
</div>
@include('admin.components.page_card_component')
@include('admin.components.users_collection_component')
@include('admin.components.create_contents_component')
@include('admin.components.file_explorer_collection_component')
@include('admin.components.albums_widget_component')

@endsection

@section('footer_includes')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?=base_url('resources/components/widget/AlbumsWidgetComponent.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/CreateContents.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/FileExplorerCollection.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/PageCardComponent.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/widget/UsersCollection.js?v=' . ADMIN_VERSION)?>"></script>
<script src="<?=base_url('resources/components/DashboardModule.js?v=' . ADMIN_VERSION)?>"></script>
@endsection