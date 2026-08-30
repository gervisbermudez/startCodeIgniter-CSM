@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <data-table
        :endpoint="endpoint"
        :colums="colums"
        :index_data="index_data"
        :pagination="true"
        :client_search="true"
        :options="options"
        :show_fab="true"
        :fab_accent="true"
        :fab_tooltip="fabTooltip"
        :empty_title="emptyTitle"
        :empty_cta="emptyCta"
        :empty_href="emptyHref"
        :confirm_title="confirmTitle"
        :confirm_body="confirmBody"
        v-on:edit="editItem"
        v-on:delete="deleteItem"
        v-on:new="newItem"></data-table>
</div>
@endsection

@section('footer_includes')
@include('admin.siteforms.siteforms_i18n')
@include('admin.components.data_table_component')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/SiteFormList.js')}}"></script>
@endsection
