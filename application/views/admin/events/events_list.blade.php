@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
@include('admin.components.data_table_component')
<div id="root">
  <data-table
    ref="eventsTable"
    :endpoint="endpoint"
    :module="'admin/events'"
    :colums="colums"
    :index_data="index_data"
    :pagination="true"
    v-on:new="newEvent"
    v-on:edit="editEvent"
    v-on:delete="deleteItem"
    v-on:archive="archiveItem"
  ></data-table>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/EventsList.js?v=' . ADMIN_VERSION)}}"></script>
@endsection