@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
@include('admin.custommodels.i18n')
<div id="root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'custommodels-items-search',
        'refreshMethod' => 'getItems()',
        'navbarShow' => '!loader',
        'placeholder' => lang('custommodels_content_search_placeholder'),
        'itemsExpr' => 'filterItems',
    ])
    <div class="row" v-cloak v-show="!loader">
        <div class="col s12">
            <p>
                <a href="{{ base_url('admin/custommodels/') }}"><?= lang('collections_back') ?></a>
            </p>
            <h3 class="page-header">{{ $h1 }}</h3>
            <div class="status-filters">
                <button type="button" class="status-chip" :class="{active: statusFilter === null}" @click="setStatusFilter(null)"><?= lang('menu_all') ?></button>
                <button type="button" class="status-chip" :class="{active: statusFilter === 1}" @click="setStatusFilter(1)"><?= lang('collections_published') ?></button>
                <button type="button" class="status-chip" :class="{active: statusFilter === 2}" @click="setStatusFilter(2)"><?= lang('collections_draft') ?></button>
            </div>
        </div>
    </div>
    <div class="pages" v-cloak v-if="!loader && filterItems.length > 0">
        <div class="row">
            <div class="col s12 m4" v-for="(item, index) in filterItems" :key="item.custom_model_content_id">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getItemImagePath(item)" alt="">
                        </div>
                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger st-accent" href='#!'
                            :data-target='"dropdown_item_" + item.custom_model_content_id'
                            :aria-label="'<?= lang('options') ?>'">
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown_item_" + item.custom_model_content_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/custommodels/editData/' + custom_model_id + '/' + item.custom_model_content_id)"><?= lang('btn_edit') ?></a></li>
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(item, index);"><?= lang('btn_delete') ?></a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <span class="card-title">
                            @{{ item.title || (i18n.fallbackTitle + ' #' + item.custom_model_content_id) }}
                            @include('admin.components.entity_card_badges', ['item' => 'item'])
                        </span>
                        <p><?= lang('collections_sort_order') ?>: @{{ item.sort_order }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && items.length == 0" v-cloak>
        <i class="material-icons large" aria-hidden="true">article</i>
        <h4 class="page-header"><?= lang('custommodels_content_no_contents') ?></h4>
        <a class="btn waves-effect st-accent" :href="base_url('admin/custommodels/addData/' + custom_model_id)"><?= lang('collections_add_item') ?></a>
    </div>
    @include('admin.components.pagination')
    <confirm-modal id="deleteModal" :title="'<?= lang('custommodels_content_confirm_delete_title') ?>'" v-on:notify="confirmCallback">
        <p><?= lang('collections_confirm_delete_item') ?></p>
    </confirm-modal>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left"
        data-delay="50" data-tooltip="<?= lang('collections_add_item') ?>"
        href="{{ base_url('admin/custommodels/addData/' . $custom_model_id) }}">
        <i class="material-icons">add</i>
    </a>
</div>
<script>
window.COLLECTION_ITEMS_MODEL_ID = <?= json_encode((int) $custom_model_id) ?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CustomModelItemsList.js')}}"></script>
@endsection
