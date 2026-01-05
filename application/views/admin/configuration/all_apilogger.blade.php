@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
  <router-view
    :endpoint="endpoint"
    :module="'config/Apilogger/'"
    :colums="colums"
    :index_data="index_data"
    :pagination="true"
  ></router-view>
</div>
@endsection

@section('footer_includes')
@include('admin.components.data_table_component')
@include('admin.components.data_edit_component')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/DataEditComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/ApiLoggerDataComponent.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
