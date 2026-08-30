@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@endsection

@section('content')
<div id="root">
    <div class="col s12 center" v-bind:class="{ hide: !loader }">
        <br><br>
        <preloader />
    </div>
    @include('admin.components.page_navbar', [
        'searchInputId' => 'albums-search',
        'refreshMethod' => 'getAlbums()',
        'navbarShow' => '!loader && albums.length > 0',
        'itemsExpr' => 'filterData',
    ])
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
                                <i v-else class="material-icons tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('draft'); ?>">edit</i>
                            </td>
                            <td>
                                <a class='dropdown-trigger' href='#!' :data-target='"dropdown" + album.album_id'><i class="material-icons">more_vert</i></a>
                                <ul :id='"dropdown" + album.album_id' class='dropdown-content'>
                                    <li><a :href="base_url('admin/pages/editar/' + album.album_id)">{{ lang('edit') }}</a></li>
                                    <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(album, index);">{{ lang('delete') }}</a></li>
                                    <li v-if="album.status == 2"><a :href="base_url('admin/pages/preview?album_id=' + album.album_id)" target="_blank">{{ lang('preview') }}</a></li>
                                    <li><a :href="base_url('/admin/gallery/items/' + album.album_id)" target="_blank">{{ lang('view_in_site') }}</a></li>
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
                            <li><a :href="base_url('admin/gallery/editar/' + album.album_id)">{{ lang('edit') }}</a></li>
                            <li><a class="modal-trigger" href="#deleteModal" v-on:click="tempDelete(album, index);">{{ lang('delete') }}</a></li>
                            <li v-if="album.status == 2"><a :href="base_url('admin/pages/preview?album_id=' + album.album_id)" target="_blank">{{ lang('preview') }}</a></li>
                            <li><a :href="base_url('/admin/gallery/items/' + album.album_id)" target="_blank">{{ lang('view_in_site') }}</a></li>
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
    <div class="container" v-if="!loader && albums.length == 0" v-cloak>
        <h4><?php echo lang('no_albums'); ?></h4>
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
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
    <a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left" data-delay="50" data-tooltip="<?php echo lang('new_album'); ?>" href="{{base_url('admin/gallery/nuevo/')}}">
        <i class="large material-icons">add</i>
    </a>
</div>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/AlbumsLists.js?v=' . ADMIN_VERSION)}}"></script>
@endsection