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
@include('admin.components.DataTableComponent')
@include('admin.components.DataEditComponent')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/DataEditComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/SiteFormList.js')}}"></script>
@endsection
