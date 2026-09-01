@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
@include('admin.custommodels.i18n')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_collections',
        'ledeKey' => 'collections_lede',
    ])
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'custommodels-search',
        'refreshMethod' => 'getModels()',
        'navbarShow' => '!loader && models.length > 0',
        'itemsExpr' => 'filterModels',
    ])
    <div class="pages" v-cloak v-if="!loader && models.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?= lang('custommodels_table_name') ?></th>
                            <th><?= lang('collections_slug') ?></th>
                            <th><?= lang('collections_items_count') ?></th>
                            <th><?= lang('custommodels_table_status') ?></th>
                            <th><?= lang('custommodels_table_options') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(model, index) in filterModels" :key="model.custom_model_id">
                            <td>
                                <a :href="base_url('admin/custommodels/items/' + model.custom_model_id)">@{{model.form_name}}</a>
                            </td>
                            <td>@{{model.slug}}</td>
                            <td>@{{model.items_count || 0}}</td>
                            <td>
                                <span v-if="model.status == 1"><?= lang('collections_enabled') ?></span>
                                <span v-else><?= lang('collections_disabled') ?></span>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!'
                                    :data-target='"dropdown_t_" + model.custom_model_id'
                                    :aria-label="'<?= lang('options') ?>'"><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown_t_" + model.custom_model_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/custommodels/items/' + model.custom_model_id)"><?= lang('collections_view_items') ?></a></li>
                                    @if(has_permisions('CREATE_CONTENT_DATA'))
                                    <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"><?= lang('collections_add_item') ?></a></li>
                                    @endif
                                    @if(has_permisions('UPDATE_FORM_CUSTOM'))
                                    <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"><?= lang('collections_edit_schema') ?></a></li>
                                    @endif
                                    <li><a href="#!" v-on:click.prevent="copySnippet(model)"><?= lang('collections_copy_snippet') ?></a></li>
                                    @if(has_permisions('DELETE_FORM_CUSTOM'))
                                    <li><a class="modal-trigger" href="#deleteModal"
                                        v-on:click="tempDelete(model, index);"><?= lang('custommodels_delete') ?></a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(model, index) in filterModels" :key="model.custom_model_id">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img src="{{ base_url('public/img/default.jpg') }}" alt="">
                        </div>
                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger st-accent" href='#!'
                            :data-target='"dropdown_c_" + model.custom_model_id'
                            :aria-label="'<?= lang('options') ?>'">
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown_c_" + model.custom_model_id' class='dropdown-content'>
                            <li><a :href="base_url('admin/custommodels/items/' + model.custom_model_id)"><?= lang('collections_view_items') ?></a></li>
                            @if(has_permisions('CREATE_CONTENT_DATA'))
                            <li><a :href="base_url('admin/custommodels/addData/' + model.custom_model_id)"><?= lang('collections_add_item') ?></a></li>
                            @endif
                            @if(has_permisions('UPDATE_FORM_CUSTOM'))
                            <li><a :href="base_url('admin/custommodels/editForm/' + model.custom_model_id)"><?= lang('collections_edit_schema') ?></a></li>
                            @endif
                            <li><a href="#!" v-on:click.prevent="copySnippet(model)"><?= lang('collections_copy_snippet') ?></a></li>
                            @if(has_permisions('DELETE_FORM_CUSTOM'))
                            <li><a class="modal-trigger" href="#deleteModal"
                                v-on:click="tempDelete(model, index);"><?= lang('custommodels_delete') ?></a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title">
                                <a :href="base_url('admin/custommodels/items/' + model.custom_model_id)">@{{model.form_name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'model'])
                            </span>
                            <div class="card-info">
                                <p v-if="model.form_description">@{{getcontentText(model.form_description)}}</p>
                                <p><code>@{{model.slug}}</code> · @{{model.template}}</p>
                                <p><?= lang('collections_items_count') ?>: @{{model.items_count || 0}} · <?= lang('collections_fields_count') ?>: @{{model.fields_count || 0}}</p>
                                <user-info v-if="model.user" :user="model.user" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && models.length == 0" v-cloak>
        <i class="material-icons large" aria-hidden="true">view_module</i>
        <h4 class="page-header"><?= lang('collections_empty') ?></h4>
        <p><?= lang('collections_empty_cta') ?></p>
        @if(has_permisions('CREATE_FORM_CUSTOM'))
        <a class="btn waves-effect st-accent" href="{{ base_url('admin/custommodels/new') }}"><?= lang('collections_new') ?></a>
        @endif
    </div>
    <confirm-modal id="deleteModal" :title="'<?= lang('collections_confirm_delete_title') ?>'" v-on:notify="confirmCallback">
        <p>
            <?= lang('collections_confirm_delete_message') ?>
        </p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_FORM_CUSTOM'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left"
        data-delay="50" data-tooltip="<?= lang('tooltip_new_collection') ?>" href="{{base_url('admin/custommodels/new')}}">
        <i class="material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CustomModelsLists.js')}}"></script>
@endsection
