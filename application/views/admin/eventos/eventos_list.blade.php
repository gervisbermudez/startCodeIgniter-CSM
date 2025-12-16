@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
  <data-table
    :endpoint="endpoint"
    :module="'admin/events'"
    :colums="colums"
    :index_data="index_data"
    :pagination="true"
    v-on:new="newEvent"
    v-on:edit="editEvent"
    v-on:delete="deleteItem"
  ></data-table>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/EventsList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection