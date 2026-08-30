@extends('admin.layouts.app')
@section('title', $title)

@section('content')
@include('admin.configuration.components.i18n')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <div class="col s12 config-content config-content--toolbar">
            <data-table
                :key="activeTab"
                :endpoint="endpoint"
                :colums="colums"
                :index_data="index_data"
                :pagination="true"
            >
                <div slot="filters" class="config-log-tabs">
                    <a href="#!" class="chip" :class="{active: activeTab == 'system'}" @click.prevent="changeTab('system')">{{ lang('config_logs_system') }}</a>
                    <a href="#!" class="chip" :class="{active: activeTab == 'api'}" @click.prevent="changeTab('api')">{{ lang('config_logs_api') }}</a>
                    <a href="#!" class="chip" :class="{active: activeTab == 'tracking'}" @click.prevent="changeTab('tracking')">{{ lang('config_logs_tracking') }}</a>
                </div>
            </data-table>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.components.data_table_component')
@include('admin.components.data_edit_component')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/DataEditComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/LogsDataComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
