@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_siteforms',
        'ledeKey' => 'siteforms_lede',
    ])
    <data-table
        ref="formsTable"
        :endpoint="endpoint"
        :colums="colums"
        :index_data="index_data"
        :pagination="true"
        :client_search="true"
        :query_params="queryParams"
        :options="options"
        :show_fab="canCreate"
        :can_update="canUpdate"
        :can_delete="canDelete"
        :fab_accent="true"
        :fab_tooltip="fabTooltip"
        :empty_title="emptyTitle"
        :empty_cta="emptyCta"
        :empty_href="emptyHref"
        :confirm_title="confirmTitle"
        :confirm_body="confirmBody"
        v-on:edit="editItem"
        v-on:delete="deleteItem"
        v-on:new="newItem"
        v-on:clear-status="currentStatus = null">
        <div slot="filters" class="filter-group" role="group" aria-label="<?= htmlspecialchars(lang('status'), ENT_QUOTES, 'UTF-8') ?>">
            <button type="button" class="status-chip" :class="{ active: currentStatus === null }" @click="setStatus(null)"><?= lang('menu_all') ?></button>
            <button type="button" class="status-chip" :class="{ active: currentStatus === 1 }" @click="setStatus(1)"><?= lang('siteforms_active') ?></button>
            <button type="button" class="status-chip" :class="{ active: currentStatus === 2 }" @click="setStatus(2)"><?= lang('siteforms_inactive') ?></button>
        </div>
    </data-table>
</div>
@endsection

@section('footer_includes')
@include('admin.siteforms.siteforms_i18n')
@include('admin.components.data_table_component')
<script src="{{base_url('resources/components/DataTableComponent.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('resources/components/SiteFormList.js')}}"></script>
@endsection
