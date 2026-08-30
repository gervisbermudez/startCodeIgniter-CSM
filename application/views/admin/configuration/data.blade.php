@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div id="root" class="configuration-root">
    <div class="row configuration-layout">
        <div class="col s12 config-content">
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
