@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
  <data-table
    :endpoint="endpoint"
    :module="'admin/eventos'"
    :colums="colums"
    :index_data="index_data"
	:pagination="true"
	v-on:new="newEvent"
  ></data-table>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
<script src="{{base_url('public/js/components/dataTable.component.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/EventsList.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection