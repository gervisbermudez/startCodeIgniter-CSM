@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection

@section('head_includes')
<style>
    .config-sidebar {
        padding-top: 10px;
        border-right: 1px solid #e0e0e0;
        min-height: calc(100vh - 64px);
    }

    .config-sidebar .collection {
        border: none;
        margin: 0;
    }

    .config-sidebar .collection .collection-item {
        border-bottom: none;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .config-sidebar .collection .collection-item:hover {
        background-color: #f5f5f5;
    }

    .config-sidebar .collection .collection-item.active {
        background-color: #e0e0e0;
        color: #000;
        font-weight: bold;
    }
    
    .config-sidebar .collection .collection-item i {
        margin-right: 10px;
        vertical-align: middle;
        color: #757575;
    }
    
    .config-sidebar .collection .collection-item.active i {
        color: #000;
    }

    .config-content {
        padding: 24px;
        background-color: #f8f9fa;
    }

    /* Gradients */
    .gradient-blue { background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); }
    .gradient-pink { background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%); }
    .gradient-green { background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%); }
    .gradient-amber { background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%); }

    /* Cards */
    .config-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .config-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }

    .card-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem !important;
        opacity: 0.15;
    }

    .card-count { margin: 0; font-weight: 700; font-size: 1.8rem; }
    .card-label { margin: 0; font-size: 0.9rem; font-weight: 500; opacity: 0.9; }

    /* Widgets */
    .dashboard-widget {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important;
        margin-bottom: 24px;
    }

    .widget-title {
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        color: #2c3e50;
        margin-bottom: 20px !important;
        display: flex;
        align-items: center;
    }

    .widget-title i { margin-right: 12px; }

    /* Health Checks */
    .alert-item {
        display: flex;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 12px;
        align-items: flex-start;
    }

    .alert-success { background: rgba(76, 175, 80, 0.1); border-left: 4px solid #4CAF50; color: #2E7D32; padding: 10px 15px; border-radius: 4px; margin-bottom: 10px; }
    .alert-warning { background: rgba(255, 152, 0, 0.1); border-left: 4px solid #FF9800; color: #E65100; padding: 10px 15px; border-radius: 4px; margin-bottom: 10px; }
    .alert-info { background-color: #e3f2fd; color: #1976d2; border: 1px solid #bbdefb; }

    .alert-content { margin-left: 12px; }
    .alert-title { display: block; font-size: 0.9rem; }
    .alert-msg { font-size: 0.8rem; opacity: 0.9; }

    /* Quick Settings */
    .quick-settings-list { margin: 0; }
    .quick-settings-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .quick-settings-list li:last-child { border-bottom: none; }

    /* System Stats */
    .stat-item { margin-bottom: 15px; }
    .stat-info { display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 6px; }
    .progress-container { background: #eee; border-radius: 10px; height: 8px; overflow: hidden; }
    .progress-bar { height: 100%; border-radius: 10px; transition: width 0.5s ease; }

    .detail-val { font-weight: 700; color: #2c3e50; font-size: 1rem; }
    .detail-label { font-size: 0.75rem; color: #95a5a6; }

    /* Activity Feed */
    .activity-feed { position: relative; padding-left: 20px; list-style: none; margin: 0; }
    .activity-feed::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 2px; background: #eee;
    }
    .activity-feed li { position: relative; margin-bottom: 16px; padding-left: 15px; }
    .activity-dot {
        position: absolute;
        left: -20px; top: 4px;
        width: 10px; height: 10px;
        background: #2196f3;
        border-radius: 50%;
        border: 2px solid #fff;
        z-index: 1;
    }
    .activity-name { display: block; font-size: 0.9rem; color: #34495e; }
    .activity-time { font-size: 0.75rem; color: #95a5a6; }

    /* Database Manager Styles */
    .database-manager { padding: 20px; }
    .section-header { margin-bottom: 30px; }
    .section-title { margin: 0; font-weight: 300; display: flex; align-items: center; }
    .section-title i { font-size: 2rem; margin-right: 10px; }
    .section-description { margin: 5px 0 0 0; font-size: 1rem; }
    
    .gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }
    .gradient-primary:hover { box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); transform: translateY(-2px); }
    
    .stats-row { margin-bottom: 20px; }
    .stats-card { border-radius: 12px; overflow: hidden; transition: all 0.3s ease; }
    .stats-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    .stats-card .card-content { display: flex; align-items: center; padding: 20px; }
    
    .stats-icon {
        width: 60px; height: 60px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; margin-right: 15px;
    }
    .stats-icon i { color: white; font-size: 2rem; }
    .stats-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stats-icon.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .stats-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    
    .stats-info { flex: 1; }
    .stats-label { display: block; font-size: 0.9rem; color: #9e9e9e; margin-bottom: 5px; }
    .stats-value { margin: 0; font-weight: 500; font-size: 1.8rem; }
    
    .backups-card { border-radius: 12px; overflow: hidden; }
    .card-title-wrapper { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
    .card-title { display: flex; align-items: center; font-size: 1.5rem; margin: 0; }
    
    .search-wrapper { position: relative; display: flex; align-items: center; }
    .search-wrapper i { position: absolute; left: 10px; color: #9e9e9e; }
    .search-wrapper input {
        padding-left: 40px; border: 1px solid #e0e0e0; border-radius: 25px;
        height: 40px; width: 250px; margin: 0;
    }
    .search-wrapper input:focus { border-color: #667eea; box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2); }
    
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 5rem; color: #e0e0e0; margin-bottom: 20px; }
    .empty-state h5 { color: #757575; margin-bottom: 10px; }
    .empty-state p { color: #9e9e9e; margin-bottom: 30px; }
    
    .backups-table-wrapper { overflow-x: auto; }
    .database-manager table thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .database-manager table thead th { color: white !important; font-weight: 500; padding: 15px 10px; }
    .database-manager table thead th i { vertical-align: middle; margin-right: 5px; }
    
    .backup-row { transition: all 0.2s ease; }
    .backup-row:hover { background-color: #f5f5f5; }
    
    .file-info { display: flex; align-items: center; }
    .file-icon { color: #667eea; margin-right: 10px; }
    .file-name { font-weight: 500; }
    
    .date-badge {
        display: inline-flex; align-items: center; padding: 5px 12px;
        background: #e3f2fd; border-radius: 15px; color: #1976d2; font-size: 0.9rem;
    }
    .date-badge i { margin-right: 5px; }
    
    .path-code { background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; color: #424242; }
    
    .size-badge {
        display: inline-block; padding: 5px 12px; background: #fff3e0;
        border-radius: 15px; color: #e65100; font-weight: 500; font-size: 0.9rem;
    }
    
    .action-buttons { display: flex; gap: 10px; justify-content: center; }
    .action-buttons .btn-floating { transition: all 0.3s ease; }
    .action-buttons .btn-floating:hover { transform: scale(1.1); }
    
    .modal h4 { display: flex; align-items: center; }
    .modal h4 i { margin-right: 10px; }
    
    @media only screen and (max-width: 600px) {
        .section-header .right-align { text-align: left !important; margin-top: 20px; }
        .btn-large { width: 100%; }
        .search-wrapper input { width: 100%; }
        .card-title-wrapper { flex-direction: column; align-items: flex-start; }
        .search-wrapper { width: 100%; margin-top: 15px; }
    }

    /* Backup List */
    .backup-mini-list { list-style: none; margin: 0; }
    .backup-mini-list li {
        display: flex;
        align-items: center;
        padding: 8px 0;
        font-size: 0.85rem;
        color: #34495e;
    }
    .backup-mini-list li i { margin-right: 10px; color: #95a5a6; }
    .backup-date { margin-left: auto; font-size: 0.75rem; color: #95a5a6; }

    /* Helper Classes */
    .text-red { color: #f44336; }
    .text-green { color: #4caf50; }
    .full-width { width: 100%; }
    .chart-caption { font-size: 0.8rem; color: #95a5a6; margin-top: 10px; }
</style>
@endsection

@section('content')
<div id="root" class="configuration-root">
    <div class="row" style="margin-bottom: 0;">
        <!-- New Sidebar -->
        <div class="col s12 m3 l2 config-sidebar hide-on-small-only">
            <div class="collection">
                <a class="collection-item" :class="{active: sectionActive == 'home'}" @click="changeSectionActive('home')">
                    <i class="material-icons">dashboard</i> {{ lang('dashboard_overview') }}
                </a>
                 <a class="collection-item" :class="{active: sectionActive == 'general'}" @click="changeSectionActive('general')">
                    <i class="material-icons">build</i> {{ lang('config_general') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'theme'}" @click="changeSectionActive('theme')">
                    <i class="material-icons">brush</i> {{ lang('config_theme') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'analytics'}" @click="changeSectionActive('analytics')">
                    <i class="material-icons">insert_chart</i> {{ lang('config_analytics') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'seo'}" @click="changeSectionActive('seo')">
                    <i class="material-icons">trending_up</i> {{ lang('config_seo') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'pixel'}" @click="changeSectionActive('pixel')">
                    <i class="material-icons">insert_chart</i> Facebook Pixel
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'database'}" @click="changeSectionActive('database')">
                    <i class="material-icons">sd_card</i> {{ lang('config_database') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'updater'}" @click="changeSectionActive('updater')">
                    <i class="material-icons">system_update_alt</i> {{ lang('config_updater') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'logger'}" @click="changeSectionActive('logger')">
                    <i class="material-icons">list</i> {{ lang('config_logger') }}
                </a>
                <a class="collection-item" :class="{active: sectionActive == 'system'}" @click="changeSectionActive('system')">
                    <i class="material-icons">settings_applications</i> Sistema
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col s12 m9 l10 config-content">
             <!-- Mobile Tabs (Visible only on small screens) -->
            <div class="hide-on-med-and-up">
                 <div class="input-field col s12">
                    <select v-model="sectionActive" @change="changeSectionActive(sectionActive)">
                        <option value="home">{{ lang('dashboard_overview') }}</option>
                        <option value="general">{{ lang('config_general') }}</option>
                        <option value="theme">{{ lang('config_theme') }}</option>
                        <option value="analytics">{{ lang('config_analytics') }}</option>
                        <option value="seo">{{ lang('config_seo') }}</option>
                        <option value="pixel">Facebook Pixel</option>
                        <option value="database">{{ lang('config_database') }}</option>
                        <option value="updater">{{ lang('config_updater') }}</option>
                        <option value="logger">{{ lang('config_logger') }}</option>
                        <option value="system">Sistema</option>
                    </select>
                </div>
            </div>

            @include('admin.configuration.components.home_dashboard')
            @include('admin.configuration.components.general_settings')
            @include('admin.configuration.components.analytics_settings')
            @include('admin.configuration.components.add_config')
            @include('admin.configuration.components.pixel_settings')
            @include('admin.configuration.components.backup_manager')
            @include('admin.configuration.components.theme_selector')
            @include('admin.configuration.components.updater_manager')
            @include('admin.configuration.components.system_settings')
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.components.configuration_component')
<script src="{{base_url('public/vendors/chartjs/Chart.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection