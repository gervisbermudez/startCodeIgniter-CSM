@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
<style>
    .input-field.search-top {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container search_results form" id="root">
    <div class="row">
        <div class="col s12">
            <h3 class="page-header">{{$h1}}</h3>
        </div>
    </div>
    <div class="col s12 center" v-bind:class="{ hide: !loader }" style="min-height: 160px;">
        <br><br>
        <preloader />
    </div>
    <div class="row" v-if="!loader">
        <div class="col s12">
            <ul class="tabs">
                <li class="tab col s3"><a class="active" href="#test1">WEBSITE <span class="badge" v-if="websiteCountResults > 0">@{{websiteCountResults}}</span></a></li>
                <li class="tab col s3"><a href="#test2">Users <span class="badge"  v-if="searchResults.users.length > 0">@{{searchResults.users.length}}</span></a></li>
                <li class="tab col s3"><a href="#test3">Files <span class="badge" v-if="searchResults.files.length > 0">@{{searchResults.files.length}}</span></a></li>
            </ul>
        </div>
    </div>
    <div class="row" v-if="!loader">
        <div class="input-field col s12">
            <i class="material-icons prefix">search</i>
            <input id="icon_prefix" type="text" class="validate" v-model="searchTerm" v-on:keyup.enter="performSearch">
            <label for="icon_prefix" class="active">Search...</label>
        </div>
    </div>
    <div v-if="!loader">
        <div class="row" v-cloak id="test1">
            <div class="col s12" v-if="searchResults.pages.length > 0">
                <h5>Pages</h5>
                <div class="row">
                    <div class="col s12 m4" v-for="(page, index) in searchResults.pages" :key="index">
                        <div class="card">
                            <div class="card-image card-image-container">
                                <img :src="getPageImagePath(page)" />
                            </div>
                            <div class="card-content">
                                <span class="card-title truncate">@{{page.title}}</span>
                                <p>@{{getcontentText(page.content)}}</p>
                            </div>
                            <div class="card-action">
                                <a :href="base_url('admin/pages/view/' + page.page_id)">View</a>
                                <a :href="base_url('admin/pages/editar/' + page.page_id)">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" v-if="searchResults.albumes.length > 0">
                <h5>Albumes</h5>
                <div class="row">
                    <div class="col s12 m4" v-for="(album, index) in searchResults.albumes" :key="index">
                        <div class="card">
                            <div class="card-image card-image-container">
                                <img :src="getAlbumImagePath(album)" />
                            </div>
                            <div class="card-content">
                                <span class="card-title">@{{album.name}}</span>
                                <p>@{{getcontentText(album.description)}}</p>
                            </div>
                            <div class="card-action">
                                <a :href="base_url('admin/gallery/items/' + album.album_id)">View</a>
                                <a :href="base_url('admin/gallery/editar/' + album.album_id)">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" v-if="searchResults.categories.length > 0">
                <h5>Categories</h5>
                <data-table v-if="searchResults.categories.length > 0" :endpoint="''" :module="'categories'"
                    :colums="categoriesColumns" :index_data="'categorie_id'" :pagination="false" :search_input="false"
                    :show_empty_input="false" :preset_data="searchResults.categories"></data-table>
            </div>
            <div class="col s12" v-if="searchResults.form_customs.length > 0">
                <h5>Form Custom</h5>
                <data-table v-if="searchResults.form_customs.length > 0" :endpoint="''" :module="'form_customs'"
                    :colums="form_customsColumns" :index_data="'user_id'" :pagination="false" :search_input="false"
                    :show_empty_input="false" :preset_data="searchResults.form_customs"></data-table>
            </div>
            <div class="col s12" v-if="searchResults.form_contents.length > 0">
                <h5>Form Contents</h5>
                <data-table v-if="searchResults.form_contents.length > 0" :endpoint="''" :module="'form_contents'"
                    :colums="form_contentsColumns" :index_data="'user_id'" :pagination="false" :search_input="false"
                    :show_empty_input="false" :preset_data="searchResults.form_contents"></data-table>
            </div>
            <div class="col s12" v-if="searchResults.siteforms.length > 0">
                <h5>SiteForms</h5>
                <data-table v-if="searchResults.siteforms.length > 0" :endpoint="''" :module="'siteforms'"
                    :colums="siteformsColumns" :index_data="'user_id'" :pagination="false" :search_input="false"
                    :show_empty_input="false" :preset_data="searchResults.siteforms"></data-table>
            </div>
            <div class="col s12" v-if="searchResults.menus.length > 0">
                <h5>Menus</h5>
                <data-table v-if="searchResults.menus.length > 0" :endpoint="''" :module="'menus'"
                    :colums="menusColumns" :index_data="'user_id'" :pagination="false" :search_input="false"
                    :show_empty_input="false" :preset_data="searchResults.menus"></data-table>
            </div>

        </div>
        <div class="row" v-cloak id="test2">
            <div class="col s12">
                <h5>Users</h5>
                <div class="row" v-cloak v-if="searchResults.users.length > 0">
                    <div class="col s12 m3" v-for="(user, index) in searchResults.users" :key="index">
                        <user-info :user="user" />
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-cloak id="test3">
            <div class="col s12">
                <h5>Files</h5>
                <div class="row" v-if="searchResults.files.length > 0">
                    <div class="col s12 m6 l4 xl3 file" v-for="(item, index) in searchResults.files" :key="index">
                        <div class="card">
                            <div class="card-image waves-effect waves-block waves-light" v-if="!isImage(item)">
                                <div class="file icon activator">
                                    <i :class="getIcon(item)"></i>
                                </div>
                            </div>
                            <div class="card-image" v-if="isImage(item)">
                                <img :src="getImagePath(item)" class="materialboxed">
                            </div>
                            <div class="card-content">
                                @{{(item.file_name + getExtention(item))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_includes')
@include('admin.components.dataTableComponent')
<script src="{{base_url('public/js/components/dataTable.component.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/searchComponent.min.js')}}"></script>
@endsection