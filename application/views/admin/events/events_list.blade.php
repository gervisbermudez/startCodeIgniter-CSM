@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
@include('admin.components.data_table_component')
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