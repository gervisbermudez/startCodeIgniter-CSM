@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
  <router-view
    :endpoint="endpoint"
    :module="'usuarios/permissions/'"
    :colums="colums"
    :index_data="index_data"
    :pagination="true"
  ></router-view>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
@include('admin.components.dataEditComponent')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/dataEdit.component.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/loggerData.component.js?v=' . ADMIN_VERSION)}}"></script>
@endsection