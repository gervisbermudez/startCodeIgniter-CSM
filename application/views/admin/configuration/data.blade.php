@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <aside class="col s12 m3 l2 config-sidebar hide-on-small-only">
            <div class="collection">
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'backups'}" @click.prevent="changeSectionActive('backups')">
                    <i class="material-icons" aria-hidden="true">storage</i>
                    <span>{{ lang('config_data_backups') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'import'}" @click.prevent="changeSectionActive('import')">
                    <i class="material-icons" aria-hidden="true">file_upload</i>
                    <span>{{ lang('config_import') }}</span>
                </a>
                <a href="#!" class="collection-item" :class="{active: sectionActive == 'export'}" @click.prevent="changeSectionActive('export')">
                    <i class="material-icons" aria-hidden="true">file_download</i>
                    <span>{{ lang('config_export') }}</span>
                </a>
            </div>
        </aside>
        <div class="col s12 m9 l10 config-content">
            <div class="hide-on-med-and-up config-mobile-nav">
                <div class="input-field">
                    <select v-model="sectionActive" @change="changeSectionActive(sectionActive)">
                        <option value="backups">{{ lang('config_data_backups') }}</option>
                        <option value="import">{{ lang('config_import') }}</option>
                        <option value="export">{{ lang('config_export') }}</option>
                    </select>
                    <label>{{ lang('menu_data') }}</label>
                </div>
            </div>
            @include('admin.configuration.components.backup_manager')
            @include('admin.configuration.components.import_panel')
            @include('admin.configuration.components.export_panel')
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.configuration.components.i18n')
<script src="{{base_url('resources/components/DataList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
