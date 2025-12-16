@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <router-view :endpoint="endpoint" :module="'api/v1/siteforms/submit/'" :colums="colums" :index_data="index_data"
        :pagination="true" v-on:delete="deleteItem" :options="options"></router-view>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
@include('admin.components.FormSiteDetails')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/FormSiteDetails.component.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/SiteFormSubmitList.js')}}"></script>
@endsection
