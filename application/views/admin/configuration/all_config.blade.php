@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <div class="col s12 config-content" :class="{ 'config-content--toolbar': sectionActive == 'general' || sectionActive == 'seo' }">
            @include('admin.configuration.components.home_dashboard')
            @include('admin.configuration.components.section_tabs')
            <div :class="{ 'config-section-body': isSiteSection || isSystemSection }">
                @include('admin.configuration.components.general_settings')
                @include('admin.configuration.components.analytics_settings')
                @include('admin.configuration.components.add_config')
                @include('admin.configuration.components.theme_selector')
                @include('admin.configuration.components.updater_manager')
                @include('admin.configuration.components.system_settings')
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.configuration.components.i18n')
@include('admin.components.configuration_component')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{base_url('resources/js/validateForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ConfigurationList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
