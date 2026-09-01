@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_categories',
        'ledeKey' => 'categories_lede',
    ])
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'categories-search',
        'refreshMethod' => 'getCategories()',
        'itemsExpr' => 'filterCategories',
    ])
    <div class="pages categories" v-cloak v-if="!loader && categories.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Author</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(categorie, index) in filterCategories" :key="index">
                            <td>@{{categorie.name}}</td>
                            <td>@{{categorie.type}}</td>
                            <td>
                                <a v-if="categorie.user" :href="base_url('admin/users/ver/' + categorie.user_id)">@{{categorie.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{categorie.date_publish ? categorie.date_publish : categorie.date_create}}
                            </td>
                            <td>
                                <i v-if="categorie.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="lang('categories_published')">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" :data-tooltip="lang('categories_not_published')">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + categorie.categorie_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + categorie.categorie_id' class='dropdown-content'>
                                    @if(has_permisions('UPDATE_CATEGORIE'))
                                    <li><a :href="base_url('admin/categories/editar/' + categorie.categorie_id)">{{ lang('edit') }}</a></li>
                                    @endif
                                    @if(has_permisions('DELETE_CATEGORIE'))
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(categorie, index);">{{ lang('delete') }}</a></li>
                                    @endif
                                    <li v-if="categorie.status == 2"><a :href="base_url('admin/categories/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                                    <li v-if="categorie.path"><a :href="base_url(categorie.path)" target="_blank">{{ lang('view_in_site') }}</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(categorie, index) in filterCategories" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(categorie)" />
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + categorie.categorie_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + categorie.categorie_id' class='dropdown-content'>
                            @if(has_permisions('UPDATE_CATEGORIE'))
                            <li><a :href="base_url('admin/categories/editar/' + categorie.categorie_id)">{{ lang('edit') }}</a></li>
                            @endif
                            @if(has_permisions('DELETE_CATEGORIE'))
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(categorie, index);">{{ lang('delete') }}</a></li>
                            @endif
                            <li v-if="categorie.status == 2"><a :href="base_url('admin/categories/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                            <li v-if="categorie.path"><a :href="base_url(categorie.path)" target="_blank">{{ lang('view_in_site') }}</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(categorie.name)" target="_blank">@{{categorie.name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'categorie'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(categorie)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="categorie.user" :user="categorie.user" />
                            </div>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{categorie.name}}
                        </span>
                        <span class="subtitle">
                            @{{categorie.subtitle}}
                        </span>
                        <ul>
                            <li><b>Publish date:</b> <br> @{{categorie.date_publish ? categorie.date_publish : categorie.date_create}}</li>
                            <li><b>Status:</b>
                                <span v-if="categorie.status == 1">
                                    {{ lang('categories_published') }}
                                </span>
                                <span v-else>
                                    {{ lang('categories_not_published') }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && categories.length == 0 && !filter" v-cloak>
        <h4>No categories found</h4>
    </div>
    @include('admin.components.pagination')
    <confirm-modal
        id="deleteModal"
        title="{{ lang('delete_category_title') }}"
        v-on:notify="confirmCallback"
    >
        <p>
            {{ lang('delete_category_confirm') }}
        </p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_CATEGORIE'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" :data-tooltip="lang('tooltip_new_category')" href="<?php echo base_url('admin/categories/nueva/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CategoriesLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
