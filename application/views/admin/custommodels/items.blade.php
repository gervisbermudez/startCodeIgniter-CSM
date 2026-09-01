@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
@include('admin.custommodels.i18n')
<div id="root">
    @include('admin.components.page_intro', [
        'title' => $h1,
        'ledeKey' => 'collections_items_lede',
    ])
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'custommodels-items-search',
        'refreshMethod' => 'getItems()',
        'navbarShow' => '!loader && collectionItemCount > 0',
        'itemsExpr' => 'filterItems',
    ])
    <p class="list-backlink" v-cloak v-show="!loader">
        <a href="{{ base_url('admin/custommodels/') }}"><?= lang('collections_back') ?></a>
    </p>
    <div class="status-filters" v-cloak v-show="!loader" v-if="collectionItemCount > 0">
        <button type="button" class="status-chip" :class="{active: statusFilter === null}" @click="setStatusFilter(null)"><?= lang('menu_all') ?></button>
        <button type="button" class="status-chip" :class="{active: statusFilter === 1}" @click="setStatusFilter(1)"><?= lang('collections_published') ?></button>
        <button type="button" class="status-chip" :class="{active: statusFilter === 2}" @click="setStatusFilter(2)"><?= lang('collections_draft') ?></button>
    </div>
    <div class="pages" v-cloak v-if="!loader && filterItems.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?= lang('custommodels_table_name') ?></th>
                            <th><?= lang('custommodels_content_table_status') ?></th>
                            <th><?= lang('collections_sort_order') ?></th>
                            <th><?= lang('custommodels_content_table_options') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in filterItems" :key="item.custom_model_content_id">
                            <td>
                                <a :href="base_url('admin/custommodels/editData/' + custom_model_id + '/' + item.custom_model_content_id)">@{{ itemTitle(item) }}</a>
                            </td>
                            <td>
                                <span v-if="item.status == 1"><?= lang('collections_published') ?></span>
                                <span v-else-if="item.status == 2"><?= lang('collections_draft') ?></span>
                            </td>
                            <td>@{{ item.sort_order }}</td>
                            <td>
                                <a class="dropdown-trigger" href="#!"
                                    :data-target='"dropdown_item_t_" + item.custom_model_content_id'
                                    :aria-label="'<?= lang('options') ?>'"><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown_item_t_" + item.custom_model_content_id' class="dropdown-content">
                                    @if(has_permisions('UPDATE_CONTENT_DATA'))
                                    <li><a :href="base_url('admin/custommodels/editData/' + custom_model_id + '/' + item.custom_model_content_id)"><?= lang('btn_edit') ?></a></li>
                                    @endif
                                    @if(has_permisions('DELETE_CONTENT_DATA'))
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(item, index);"><?= lang('btn_delete') ?></a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(item, index) in filterItems" :key="item.custom_model_content_id">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getItemImagePath(item)" alt="">
                        </div>
                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger st-accent" href="#!"
                            :data-target='"dropdown_item_c_" + item.custom_model_content_id'
                            :aria-label="'<?= lang('options') ?>'">
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown_item_c_" + item.custom_model_content_id' class="dropdown-content">
                            @if(has_permisions('UPDATE_CONTENT_DATA'))
                            <li><a :href="base_url('admin/custommodels/editData/' + custom_model_id + '/' + item.custom_model_content_id)"><?= lang('btn_edit') ?></a></li>
                            @endif
                            @if(has_permisions('DELETE_CONTENT_DATA'))
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(item, index);"><?= lang('btn_delete') ?></a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-content">
                        <span class="card-title">
                            @{{ itemTitle(item) }}
                            @include('admin.components.entity_card_badges', ['item' => 'item'])
                        </span>
                        <p><?= lang('collections_sort_order') ?>: @{{ item.sort_order }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && isCollectionEmpty" v-cloak>
        <i class="material-icons large" aria-hidden="true">article</i>
        <h4 class="page-header"><?= lang('custommodels_content_no_contents') ?></h4>
        @if(has_permisions('CREATE_CONTENT_DATA'))
        <a class="btn waves-effect st-accent" :href="base_url('admin/custommodels/addData/' + custom_model_id)"><?= lang('collections_add_item') ?></a>
        @endif
    </div>
    <div class="container center" v-if="!loader && isStatusFilterEmpty" v-cloak>
        <i class="material-icons large" aria-hidden="true">filter_list</i>
        <h4 class="page-header">@{{ statusEmptyMessage }}</h4>
    </div>
    @include('admin.components.pagination')
    <confirm-modal id="deleteModal" :title="'<?= lang('custommodels_content_confirm_delete_title') ?>'" v-on:notify="confirmCallback">
        <p><?= lang('collections_confirm_delete_item') ?></p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_CONTENT_DATA'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left"
        data-delay="50" data-tooltip="<?= lang('collections_add_item') ?>"
        href="{{ base_url('admin/custommodels/addData/' . $custom_model_id) }}">
        <i class="material-icons">add</i>
    </a>
</div>
@endif
<script>
window.COLLECTION_ITEMS_MODEL_ID = <?= json_encode((int) $custom_model_id) ?>;
window.COLLECTION_ITEMS_COUNT = <?= json_encode(isset($items_count) ? (int) $items_count : 0) ?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CustomModelItemsList.js')}}"></script>
@endsection
