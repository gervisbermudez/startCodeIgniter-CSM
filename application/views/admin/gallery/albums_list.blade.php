@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
<div id="root">
    @include('admin.components.page_intro', [
        'titleKey' => 'menu_albums',
        'ledeKey' => 'albums_lede',
    ])
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'albums-search',
        'refreshMethod' => 'getAlbums(currentStatus)',
        'navbarShow' => '!loader && albums.length > 0',
        'itemsExpr' => 'filterData',
    ])
    <div class="status-filters" v-cloak v-show="!loader">
        <button type="button" class="status-chip" :class="{active: currentStatus === null}" @click="getAlbums(null)">
            <?php echo lang('menu_all'); ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 1}" @click="getAlbums(1)">
            <?php echo lang('published'); ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 2}" @click="getAlbums(2)">
            <?php echo lang('draft'); ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 3}" @click="getAlbums(3)">
            <?php echo lang('archived'); ?>
        </button>
        <button type="button" class="status-chip" :class="{active: currentStatus === 0}" @click="getAlbums(0)">
            <?php echo lang('deleted'); ?>
        </button>
    </div>
    <div class="pages" v-cloak v-if="!loader && albums.length > 0">
        <div class="row" v-if="tableView">
            <div class="col s12">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo lang('album_title'); ?></th>
                            <th><?php echo lang('description'); ?></th>
                            <th><?php echo lang('author'); ?></th>
                            <th><?php echo lang('publish_date'); ?></th>
                            <th><?php echo lang('status'); ?></th>
                            <th><?php echo lang('options'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(album, index) in filterData" :key="index">
                            <td>@{{album.name}}</td>
                            <td>@{{getcontentText(album.description)}}</td>
                            <td>
                                <a v-if="album.user" :href="album.user.get_profileurl()">@{{album.user.get_fullname()}}</a>
                                <span v-else>-</span>
                            </td>
                            <td>
                                @{{album.date_publish ? album.date_publish : album.date_create}}
                            </td>
                            <td>
                                <i v-if="album.status == 1" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('published'); ?>">publish</i>
                                <i v-else-if="album.status == 2" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('draft'); ?>">edit</i>
                                <i v-else-if="album.status == 3" class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('archived'); ?>">archive</i>
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('deleted'); ?>">delete_outline</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + album.album_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + album.album_id' class='dropdown-content'>
                                    @if(has_permisions('UPDATE_GALLERY'))
                                    <li><a :href="base_url('admin/gallery/editar/' + album.album_id)">{{ lang('edit') }}</a></li>
                                    @endif
                                    @if(has_permisions('DELETE_GALLERY'))
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(album, index);">{{ lang('delete') }}</a></li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col s12 m4" v-for="(album, index) in filterData" :key="index">
                <div class="card page-card album">
                    <div class="card-image">
                        <div class="card-image-container">
                            <img :src="getPageImagePath(album, 0)" class="bottom"/>
                            <img :src="getPageImagePath(album, 1)" class="top"/>
                        </div>

                        <a class="btn-floating halfway-fab waves-effect waves-light dropdown-trigger" href='#!' :data-target='"dropdown" + album.album_id'>
                            <i class="material-icons">more_vert</i></a>
                        <ul :id='"dropdown" + album.album_id' class='dropdown-content'>
                            @if(has_permisions('UPDATE_GALLERY'))
                            <li><a :href="base_url('admin/gallery/editar/' + album.album_id)">{{ lang('edit') }}</a></li>
                            @endif
                            @if(has_permisions('DELETE_GALLERY'))
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(album, index);">{{ lang('delete') }}</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-content">
                        <div>
                            <span class="card-title"><a :href="base_url('/admin/gallery/items/' + album.album_id)">@{{album.name}}</a>
                                @include('admin.components.entity_card_badges', ['item' => 'album'])
                            </span>
                            <div class="card-info">
                                <p>
                                    @{{getcontentText(album.description)}}
                                </p>
                                <span class="activator right"><i class="material-icons">more_vert</i></span>
                                <user-info v-if="album.user" :user="album.user" />
                            </div>
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
                            <li><b><?php echo lang('publish_date'); ?>:</b> <br> @{{album.date_publish ? album.date_publish : album.date_create}}</li>
                            <li><b><?php echo lang('status'); ?>:</b>
                                <span v-if="album.status == 1">
                                    <?php echo lang('published'); ?>
                                </span>
                                <span v-else-if="album.status == 2">
                                    <?php echo lang('draft'); ?>
                                </span>
                                <span v-else-if="album.status == 3">
                                    <?php echo lang('archived'); ?>
                                </span>
                                <span v-else>
                                    <?php echo lang('deleted'); ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container center" v-if="!loader && albums.length == 0" v-cloak>
        <i class="material-icons large grey-text">perm_media</i>
        <p class="page-header"><?php echo lang('albums_empty'); ?></p>
        <a href="{{ base_url('admin/gallery/new') }}" class="btn"><?php echo lang('albums_empty_cta'); ?></a>
    </div>
    <confirm-modal 
        id="deleteModal" 
        title="<?php echo lang('confirm_delete'); ?>"
        v-on:notify="confirmCallback"
    >
        <p>
            <?php echo lang('delete_album_question'); ?>
        </p>
    </confirm-modal>
</div>
@if(has_permisions('CREATE_GALLERY'))
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large waves-effect st-accent tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('new_album'); ?>" href="{{base_url('admin/gallery/nuevo/')}}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endif
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/AlbumsLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
