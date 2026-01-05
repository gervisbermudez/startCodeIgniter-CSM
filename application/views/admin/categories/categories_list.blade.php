@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@endsection
@section('content')
<div id="root">
    <div class="col s12 center" v-show="loader">
        <br><br>
        <preloader />
    </div>
    <nav class="page-navbar" v-cloak v-show="!loader && categories.length > 0">
        <div class="nav-wrapper">
            <form>
                <div class="input-field">
                    <input class="input-search" type="search" placeholder="Search..." v-model="filter">
                    <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                    <i class="material-icons" v-on:click="resetFilter();">close</i>
                </div>
            </form>
            <ul class="right hide-on-med-and-down">
                <li><a href="#!" v-on:click="toggleView();"><i class="material-icons">view_module</i></a></li>
                <li><a href="#!" v-on:click="getCategories();"><i class="material-icons">refresh</i></a></li>
                <li>
                    <a href="#!" class='dropdown-trigger' data-target='dropdown-options'><i class="material-icons">more_vert</i></a>
                    <!-- Dropdown Structure -->
                    <ul id='dropdown-options' class='dropdown-content'>
                        <li><a href="#">{{ lang('archive') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="categories" v-cloak v-if="!loader && categories.length > 0">
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
                                    <li><a :href="base_url('admin/categories/editar/' + categorie.categorie_id)">{{ lang('edit') }}</a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(categorie, index);">{{ lang('delete') }}</a></li>
                                    <li v-if="categorie.status == 2"><a :href="base_url('admin/categories/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                                    <li><a :href="base_url(categorie.path)" target="_blank">{{ lang('archive') }}</a></li>
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
                            <li><a :href="base_url('admin/categories/editar/' + categorie.categorie_id)">{{ lang('edit') }}</a></li>
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(categorie, index);">{{ lang('delete') }}</a></li>
                            <li v-if="categorie.status == 2"><a :href="base_url('admin/categories/preview?categorie_id=' + categorie.categorie_id)" target="_blank">Preview</a></li>
                            <li><a :href="base_url(categorie.path)" target="_blank">{{ lang('archive') }}</a></li>
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url(categorie.name)" target="_blank">@{{categorie.name}}</a> <i v-if="categorie.visibility == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Public">public</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="Private">lock</i>
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(categorie)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <ul>
                                    <li>
                                        Type: @{{categorie.type}}
                                    </li>
                                    <li class="truncate">
                                        Author: <a :href="base_url('admin/users/ver/' + categorie.user_id)">@{{categorie.user.user_data.nombre}} @{{categorie.user.user_data.apellido}}</a>
                                    </li>
                                </ul>
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
    <div class="container" v-if="!loader && categories.length == 0" v-cloak>
        <h4>No categories found</h4>
    </div>
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
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" :data-tooltip="lang('tooltip_new_category')" href="<?php echo base_url('admin/categories/nueva/') ?>">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/CategoriesLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
