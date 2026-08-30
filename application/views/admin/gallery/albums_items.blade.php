@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/header.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/lightbox2/dist/css/lightbox.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/fileinput/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/font-awesome/css/all.min.css')?>">
@endsection

@section('content')
<div class="container" id="root">
    <div class="header" v-cloak>
        <div class="row" v-show="!loader && album.items.length > 0">
            <div class="col s12">
                <span class="page-header">@{{album.name}}</span>
                <a class='dropdown-trigger right' href='#!' :data-target='"album" + album.album_id'><i
                        class="material-icons">more_vert</i></a>
                <ul :id='"album" + album.album_id' class='dropdown-content'>
                    <li><a :href="base_url('admin/gallery/editar/' + album.album_id)"><?php echo lang('edit'); ?></a></li>
                    <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                    <li v-if="album.status == 2"><a :href="base_url('admin/pages/preview?album_id=' + album.album_id)"
                            target="_blank">Preview</a></li>
                    <li><a :href="base_url(album.path)" target="_blank">Archivar</a></li>
                </ul>
            </div>
            <div class="col s12">
                <span class="page-subheader">@{{getcontentText(album.description)}}</span>
            </div>
            <div class="col s12">
                <div class="author">
                    Create by: <br />
                </div>
                <user-info v-if="album.user.user_id" :user="album.user" />
            </div>
            <div class="col s12">
                <ul>
                    <li><b>Fecha de publicacion:</b>
                        @{{album.date_publish ? timeAgo(album.date_publish) : timeAgo(album.date_create)}}</li>
                    <li><b>Estado:</b>
                        <span v-if="album.status == 1">
                            Publicado
                        </span>
                        <span v-else>
                            Borrador
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'album-items-search',
        'refreshMethod' => 'getPages()',
        'navbarShow' => '!loader && album.items.length > 0',
        'itemsExpr' => 'filterData',
    ])
    <div class="pages" v-cloak v-if="!loader && album.items.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th>Album Title</th>
                            <th>Description</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(album, index) in filterData" :key="index">
                            <td>@{{album.name}}</td>
                            <td>@{{getcontentText(album.description)}}</td>
                            <td>
                                @{{album.date_publish ? album.date_publish : album.date_create}}
                            </td>
                            <td>
                                <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" data-tooltip="Publicado">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    data-tooltip="Borrador">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + album.album_item_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + album.album_item_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/pages/editar/' + album.album_item_id)"><?php echo lang('edit'); ?></a>
                                    </li>
                                    <li><a href="#!" v-on:click="deletePage(album, index);">Borrar</a></li>
                                    <li v-if="album.status == 2"><a
                                            :href="base_url('admin/pages/preview?album_item_id=' + album.album_item_id)"
                                            target="_blank">Preview</a></li>
                                    <li><a :href="base_url(album.path)" target="_blank">Archivar</a></li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(album, index) in filterData" :key="index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img class="materialboxed" :src="getPageImagePath(album)" />
                        </div>
                    </div>
                    <div class="card-content">
                        <span class="card-title">@{{album.name}}
                            @include('admin.components.entity_card_badges', ['item' => 'album'])
                        </span>
                        <div class="card-info">
                            <p>
                                @{{getcontentText(album.description)}}
                            </p>
                            <span class="activator right"><i class="material-icons">more_vert</i></span>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{album.name}}
                        </span>
                        <span class="subtitle">
                            @{{getcontentText(album.description)}}
                        </span>
                        <ul>
                            <li><b>Fecha de publicacion:</b> <br>
                                @{{album.date_publish ? album.date_publish : album.date_create}}</li>
                            <li><b>Estado:</b>
                                <span v-if="album.status == 1">
                                    Publicado
                                </span>
                                <span v-else>
                                    Borrador
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" v-if="!loader && album.items.length == 0" v-cloak>
        <h4>No hay Items</h4>
    </div>
</div>

@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/AlbumsItemsLists.js')}}"></script>
@endsection