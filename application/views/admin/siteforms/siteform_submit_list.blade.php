@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_siteforms_submissions',
        'ledeKey' => 'siteforms_inbox_lede',
    ])
    <router-view
        ref="view"
        :endpoint="endpoint"
        :colums="colums"
        :index_data="index_data"
        :pagination="true"
        :client_search="true"
        :options="options"
        :show_fab="false"
        :query_params="queryParams"
        :empty_title="emptyTitle"
        :confirm_title="confirmTitle"
        :confirm_body="confirmBody"
        v-on:delete="deleteItem"
        v-on:archive="archiveItem"></router-view>
</div>
@endsection

@section('footer_includes')
@include('admin.siteforms.siteforms_i18n')
@include('admin.components.data_table_component')
@include('admin.components.form_site_details_component')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/FormSiteDetailsComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/SiteFormSubmitList.js')}}"></script>
@endsection
