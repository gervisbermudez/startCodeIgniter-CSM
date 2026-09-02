@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <div class="col s12 config-content">
            <div class="config-section-nav">
                <div class="config-section-tabs" role="tablist" aria-label="{{ lang('config_data') }}">
                    <button type="button" class="status-chip" :class="{active: sectionActive == 'backups'}" v-on:click="changeSectionActive('backups')" role="tab" :aria-selected="sectionActive == 'backups' ? 'true' : 'false'">{{ lang('config_data_backups') }}</button>
                    <button type="button" class="status-chip" :class="{active: sectionActive == 'export'}" v-on:click="changeSectionActive('export')" role="tab" :aria-selected="sectionActive == 'export' ? 'true' : 'false'">{{ lang('config_export') }}</button>
                    <button type="button" class="status-chip" :class="{active: sectionActive == 'import'}" v-on:click="changeSectionActive('import')" role="tab" :aria-selected="sectionActive == 'import' ? 'true' : 'false'">{{ lang('config_import') }}</button>
                </div>
            </div>
            <div class="config-section-body">
            @include('admin.configuration.components.backup_manager')
            @include('admin.configuration.components.import_panel')
            @include('admin.configuration.components.export_panel')
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.configuration.components.i18n')
<script src="{{base_url('resources/components/DataList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
