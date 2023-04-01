@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <router-view :endpoint="endpoint" :module="'usuarios/permissions/'" :colums="colums" :index_data="index_data"
        :pagination="true" v-on:edit="editItem" v-on:delete="deleteItem" v-on:new="newItem"></router-view>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
@include('admin.components.dataEditComponent')
<script src="{{base_url('public/js/components/dataTable.component.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/dataEdit.component.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/SiteFormList.min.js')}}"></script>
@endsection
