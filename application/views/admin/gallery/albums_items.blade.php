@extends('admin.layouts.app')

@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/file_explorer.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/css/admin/header.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/vendors/lightbox2/src/css/lightbox.css')?>">
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
                    @if(has_permisions('UPDATE_GALLERY'))
                    <li><a :href="base_url('admin/gallery/editar/' + album.album_id)"><?php echo lang('edit'); ?></a></li>
                    @endif
                </ul>
            </div>
            <div class="col s12">
                <span class="page-subheader">@{{getcontentText(album.description)}}</span>
            </div>
            <div class="col s12">
                <div class="author">
                    <?php echo lang('albums_created_by'); ?>: <br />
                </div>
                <user-info v-if="album.user.user_id" :user="album.user" />
            </div>
            <div class="col s12">
                <ul>
                    <li><b><?php echo lang('publish_date'); ?>:</b>
                        @{{album.date_publish ? timeAgo(album.date_publish) : timeAgo(album.date_create)}}</li>
                    <li><b><?php echo lang('status'); ?>:</b>
                        <span v-if="album.status == 1">
                            <?php echo lang('published'); ?>
                        </span>
                        <span v-else>
                            <?php echo lang('draft'); ?>
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
                            <th><?php echo lang('album_title'); ?></th>
                            <th><?php echo lang('description'); ?></th>
                            <th><?php echo lang('publish_date'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('options'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in filterData" :key="item.album_item_id || index">
                            <td>@{{item.name}}</td>
                            <td>@{{getcontentText(item.description || '')}}</td>
                            <td>
                                @{{item.date_publish ? item.date_publish : item.date_create}}
                            </td>
                            <td>
                                <i v-if="item.status == 1" class="material-icons tooltipped" data-position="left"
                                    data-delay="50" data-tooltip="<?php echo lang('published'); ?>">publish</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50"
                                    data-tooltip="<?php echo lang('draft'); ?>">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + item.album_item_id'><i
                                        class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + item.album_item_id' class='dropdown-content'>
                                    @if(has_permisions('DELETE_GALLERY'))
                                    <li><a href="#!" v-on:click="deletePage(item, index);"><?php echo lang('delete'); ?></a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(item, index) in filterData" :key="item.album_item_id || index">
                <div class="card page-card">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img class="materialboxed" :src="getPageImagePath(item)" />
                        </div>
                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!'
                            :data-target='"dropdown-card" + item.album_item_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown-card" + item.album_item_id' class='dropdown-content'>
                            @if(has_permisions('DELETE_GALLERY'))
                            <li><a href="#!" v-on:click="deletePage(item, index);"><?php echo lang('delete'); ?></a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-content">
                        <span class="card-title">@{{item.name}}
                            @include('admin.components.entity_card_badges', ['item' => 'item'])
                        </span>
                        <div class="card-info">
                            <p>
                                @{{getcontentText(item.description)}}
                            </p>
                            <span class="activator right"><i class="material-icons">more_vert</i></span>
                        </div>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">
                            <i class="material-icons right">close</i>
                            @{{item.name}}
                        </span>
                        <span class="subtitle">
                            @{{getcontentText(item.description)}}
                        </span>
                        <ul>
                            <li><b><?php echo lang('publish_date'); ?>:</b> <br>
                                @{{item.date_publish ? item.date_publish : item.date_create}}</li>
                            <li><b><?php echo lang('status'); ?>:</b>
                                <span v-if="item.status == 1">
                                    <?php echo lang('published'); ?>
                                </span>
                                <span v-else>
                                    <?php echo lang('draft'); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && album.items.length == 0" v-cloak>
        <i class="material-icons large grey-text">perm_media</i>
        <p class="page-header"><?php echo lang('albums_items_empty'); ?></p>
        <a :href="base_url('admin/gallery/editar/' + album.album_id)" class="btn"><?php echo lang('albums_items_empty_cta'); ?></a>
    </div>
</div>

@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/AlbumsItemsLists.js')}}"></script>
@endsection